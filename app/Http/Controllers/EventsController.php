<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Event;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;

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
        $video = \App\YoutubeVideo::findOrFail($youtubeId);
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
        $event = new \App\Event();
        $event->uuid = 'Ikke en UUID';
        $event->title = $request->title;
        $event->description = $request->description;
        $event->vortex_url = $request->vortex_url;
        $event->facebook_id = $request->facebook_id;
        $event->start_date = new Carbon($request->start_date);
        $event->save();

        // Organizers: TODO

        $presentation = new \App\Presentation();
        $presentation->event_id = $event->id;
        $presentation->start_time = $request->p1_start_time;
        $presentation->end_time = $request->p1_end_time;
        // ...
        $presentation->save();

        if ($request->has('p1_person1')) {
            // TODO
        }

        if ($request->has('p1_youtube_id')) {
            $video = \App\YoutubeVideo::where('youtube_id', '=', $request->p1_youtube_id)->first();
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $event = \App\Event::findOrFail($id);

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
