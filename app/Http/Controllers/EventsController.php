<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventResource;
use App\Http\Requests;
use App\Presentation;
use App\YoutubeVideo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $events = Event::all();

        return response()->view('events.index', ['events' => $events]);
    }

    public function scrapeYouTubePage($youtubeId, &$data)
    {
        $video = YoutubeVideo::findOrFail($youtubeId);
        $data['p1_youtube_id'] = $video->youtube_id;
        $data['title'] = $video->title;
        $data['description'] = $video->description;
    }

    public function scrapeVortexPage(&$data)
    {
        $vortex_pattern1 = '/(https?:\/\/www.ub.uio.no\/om\/aktuelt\/arrangementer\/[^\s]+)/';
        $vortex_pattern2 = '/(https?:\/\/www.ub.uio.no\/english\/about\/news-and-events\/events\/[^\s]+)/';
        $fb_pattern = '/https?:\/\/www\.facebook\.com\/events\/([0-9]+)/';

        if (!preg_match($vortex_pattern1, $data['description'], $matches)) {
            if (!preg_match($vortex_pattern2, $data['description'], $matches)) {
                return;
            }
        }

        $data['vortex_url'] = $matches[1];
        $data['description'] = preg_replace($vortex_pattern1, '', $data['description']);
        $data['description'] = preg_replace($vortex_pattern2, '', $data['description']);

        $data['description'] = preg_replace('/\n\n\n/', "\n\n", $data['description']);
        $data['description'] = preg_replace('/\n\n\n/', "\n\n", $data['description']);

        $vortex = app('webdav')->get($data['vortex_url']);

        if (isset($vortex->properties->{'start-date'})) {
            $dts = explode(' ', $vortex->properties->{'start-date'});
            $data['start_date'] = $dts[0];
            $data['p1_start_time'] = $dts[1];
        }

        if (isset($vortex->properties->{'end-date'})) {
            $dts = explode(' ', $vortex->properties->{'end-date'});
            $data['p1_end_time'] = $dts[1];
        }

        if (preg_match($fb_pattern, $vortex->properties->content, $matches2)) {
            $data['facebook_id'] = $matches2[1];
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $data = [
            'title' => '',
            'description' => '',
            'vortex_url' => '',
            'facebook_id' => '',
            'youtube_playlist_id' => '',
            'start_date' => '',

            'p1_youtube_id' => '',
            'p1_youtube_create' => true,
            'p1_person1' => '',
            'p1_start_time' => '15:00',
            'p1_end_time' => '16:00',
        ];
        if ($request->has('from_youtube_video')) {
            $data['p1_youtube_create'] = false;
            $this->scrapeYouTubePage($request->get('from_youtube_video'), $data);
            $this->scrapeVortexPage($data);
            $data['description'] = trim($data['description']);
        }
        return response()->view('events.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $event = new Event();
        $event->uuid = Uuid::uuid1()->toString();  // Time-based version1 string (for now)
        $event->title = $request->title;
        $event->description = $request->description;
        $event->vortex_url = $request->vortex_url;
        $event->facebook_id = $request->facebook_id;
        $event->start_date = new Carbon($request->start_date);
        $event->save();

        // Organizers: TODO

        $presentation = new Presentation();
        $presentation->event_id = $event->id;
        $presentation->start_time = $request->p1_start_time;
        $presentation->end_time = $request->p1_end_time;
        // ...
        $presentation->save();

        if ($request->has('p1_person1')) {
            // TODO
        }

        if ($request->has('p1_youtube_id')) {
            $video = YoutubeVideo::where('youtube_id', '=', $request->p1_youtube_id)->first();
            if (is_null($video)) {
                die("TODO: Video not found, redirect back with meaningful error message");
            }
            $video->presentation_id = $presentation->id;
            $video->save();
        }

        return redirect()->action('EventsController@show', $event->id)
            ->with('status', 'Arrangementet ble opprettet.');
    }

    /**
     * Display edit form for the resources
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editResources($id)
    {
        $event = Event::findOrFail($id);
        $data = [
            'event' => $event,
            'resources' => $event->resources,
        ];
        return response()->view('events.resources', $data);
    }

    public function updateResources($id, Request $request)
    {
        $event = Event::findOrFail($id);
        foreach ($event->resources as $resource) {
            if ($request->has('attribution_' . $resource->id)) {
                $resource->attribution = $request->get('attribution_' . $resource->id);
            }
            if ($request->has('license_' . $resource->id)) {
                $resource->license = $request->get('license_' . $resource->id);
            }
            $resource->save();
        }
        return redirect()->action('EventsController@editResources', $event->id)
            ->with('status', 'Lagret.');

    }

    public function storeResource($id, Request $request)
    {

        $event = Event::findOrFail($id);

        $this->validate($request, [
            'file' => 'image|max:10000',
        ]);
        $file = $request->file('file');

        if ($file->isValid()) {

            $destination_path = public_path('uploads');

            list($width, $height, $type, $attr) = getimagesize($file->getPathname());

            $resource = $event->resources()->create([
                'original_filename' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'filetype' => 'image',
                'width' => $width,
                'height' => $height,
            ]);

            $extension  = $file->guessExtension();
            $filename = sha1($resource->id) . '.' . $extension;
            $request->file('file')->move($destination_path, $filename);


            $resource->filename = $filename;
            $resource->save();

            return response()->json($resource->id, 200);
        } else {
            return response()->json('errors', 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = Event::findOrFail($id);

        $vortex = app('webdav')->get($event->vortex_url);
        // print_r($vortex);die;

        $data = [
            'event' => $event,
            'vortex' => $vortex,
        ];

        return response()->view('events.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        die('neeeei');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
