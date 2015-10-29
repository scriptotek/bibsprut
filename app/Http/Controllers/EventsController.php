<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class EventsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            'youtube_id' => '',
            'vortex_url' => '',
            'facebook_url' => '',
            'person_1' => '',
        ];
        if ($request->has('from_youtube_video')) {
            $video = \App\YoutubeVideo::findOrFail($request->get('from_youtube_video'));
            $data['title'] = $video->title;
            $data['description'] = $video->description;
            $data['youtube_id'] = $video->youtube_id;

            $vortex_pattern = '/(https?:\/\/www.ub.uio.no\/om\/aktuelt\/arrangementer\/[^\s].*)$/';
            if (preg_match($vortex_pattern, $data['description'], $matches)) {
                $data['vortex_url'] = $matches[1];
                $data['description'] = preg_replace($vortex_pattern, '', $data['description']);
            }

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
        $event->title = $request->title;
        $event->description = $request->description;
        $event->vortex_url = $request->vortex_url;
        $event->save();

        // Organizers: TODO

        $presentation = new \App\Presentation();
        $presentation->event_id = $event->id;
        // ...
        $presentation->save();

        if ($request->has('person_1')) {
            // TODO
        }

        if ($request->has('youtube_id')) {
            $video = \App\YoutubeVideo::where('youtube_id', '=', $request->youtube_id)->first();
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
        //
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
