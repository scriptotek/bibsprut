<?php

namespace App\Http\Controllers;

use App\Event;
use App\EventResource;
use App\Http\Requests;
use App\Presentation;
use App\YoutubeVideo;
use App\YoutubePlaylist;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

class EventsController extends Controller
{
    /*
    public function create(Request $request)
    {
        $data = [
            'uuid' => '',
            'title' => '',
            'intro' => '',
            'description' => '',
            'vortex_url' => '',
            'facebook_id' => '',
            'youtube_playlist_id' => -1,
            'youtube_playlists' => $this->getYoutubePlaylists(),
            'start_date' => '',
            'location' => 'Realfagsbiblioteket',

            'p1_youtube_id' => '',
            'p1_youtube_create' => true,
            'p1_person1' => '',
            'p1_start_time' => '15:00',
            'p1_end_time' => '16:00',
        ];
        if ($request->has('from_recording')) {
            $data['p1_youtube_create'] = false;
            $this->scrapeYouTubePage($request->get('from_recording'), $data);
            $vortexUrl = $this->vortexUrlFromText($data['description']);
            if (!is_null($vortexUrl)) {
                $this->scrapeVortexPage($data, $vortexUrl);
            }
            $data['description'] = trim($data['description']);
        }
        if ($request->has('from_vortex')) {
            // Make sure it's actually a vortex url:
            $vortexUrl = $this->vortexUrlFromText($request->get('from_vortex'));
            if (!is_null($vortexUrl)) {
                $this->scrapeVortexPage($data, $vortexUrl);
            }
        }
        return response()->view('events.create', $data);
    }


    public function store(Request $request)
    {
        $event = new Event();
        $event->uuid = Uuid::uuid1()->toString();  // Time-based version1 string (for now)
        $event->title = $request->title;
        $event->intro = $request->intro;
        $event->description = $request->description;
        $event->vortex_url = $request->vortex_url;
        $event->facebook_id = $request->facebook_id;
        $event->start_date = new Carbon($request->start_date);
        $event->location = $request->location;

        if (!$request->youtube_playlist_id) {
            $event->youtube_playlist_id = null;
        } else {
            $youtubePlaylist = YoutubePlaylist::find($request->youtube_playlist_id);
            $event->youtube_playlist_id = $youtubePlaylist->id;
        }

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
            $recording = Recording::where('youtube_id', '=', $request->p1_youtube_id)->first();
            if (is_null($recording)) {
                die("TODO: Video not found, redirect back with meaningful error message");
            }
            $recording->presentation_id = $presentation->id;
            $recording->save();
        }

        return redirect()->action('EventsController@show', $event->id)
            ->with('status', 'Arrangementet ble opprettet.');
    }

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

            // \Storage::disk('cloud')->put(, file_get_contents($file->getPathname()) );

            $response = app('webdav')->put(
                'om/aktuelt/arrangementer/ureal/bilder/' . $filename,
                file_get_contents($file->getPathname())
            );

            if (!$response) {
                return response()->json('WebDav storage failed', 400);
            }

            $request->file('file')->move($destination_path, $filename);

            $resource->filename = $filename;
            $resource->save();

            return response()->json($resource->id, 200);
        } else {
            return response()->json('errors', 400);
        }
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        $vortex = app('webdav')->get($event->vortex_url);

        $data = [
            'event' => $event,
            'vortex' => $vortex,
        ];

        return response()->view('events.show', $data);
    }


    public function edit($id)
    {
        $event = Event::findOrFail($id);
        $p1 = $event->presentations[0];

        $data = [
            'uuid' => $event->uuid,
            'id' => $event->id,
            'title' => $event->title,
            'intro' => $event->intro,
            'description' => $event->description,
            'vortex_url' => $event->vortex_url,
            'facebook_id' => $event->facebook_id,
            'youtube_playlists' => $this->getYoutubePlaylists(),
            'youtube_playlist_id' => $event->youtube_playlist_id,
            'start_date' => $event->start_date,
            'location' => $event->location,

            'p1_youtube_id' => isset($p1->recording) ? $p1->recording->youtube_id : '',
            'p1_youtube_create' => false,
            'p1_person1' => '',
            'p1_start_time' => $p1->start_time,
            'p1_end_time' => $p1->end_time,
        ];

        return response()->view('events.create', $data);
    }

    public function update($id, Request $request)
    {
        $event = Event::findOrFail($id);
        $event->title = $request->title;
        $event->intro = $request->intro;
        $event->description = $request->description;
        $event->vortex_url = $request->vortex_url;
        $event->facebook_id = $request->facebook_id;
        $event->start_date = new Carbon($request->start_date);
        $event->location = $request->location;
        if (!$request->youtube_playlist_id) {
            $event->youtube_playlist_id = null;
        } else {
            $youtubePlaylist = YoutubePlaylist::find($request->youtube_playlist_id);
            $event->youtube_playlist_id = $youtubePlaylist->id;
        }
        $event->save();

        // Organizers: TODO

        $p1 = $event->presentations[0];
        $p1->start_time = $request->p1_start_time;
        $p1->end_time = $request->p1_end_time;
        // ...
        $p1->save();

        if ($request->has('p1_person1')) {
            // TODO
        }

        if ($request->has('p1_youtube_id')) {
            $recording = Recording::where('presentation_id', '=', $p1->id)->first();
            if (!is_null($recording)) {
                if ($recording->id != $request->p1_youtube_id) {
                    $recording->presentation_id = null;
                }
            }

            $recording = Recording::where('youtube_id', '=', $request->p1_youtube_id)->first();
            if (is_null($recording)) {
                die("TODO: Video not found, redirect back with meaningful error message");
            }
            $recording->presentation_id = $p1->id;
            $recording->save();
        }

        return redirect()->action('EventsController@show', $event->id)
            ->with('status', 'Arrangementet ble oppdatert.');
    }


    public function destroy($id)
    {
        //
    }
    */
}
