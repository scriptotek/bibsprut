<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\GoogleAccount;
use App\Harvest;
use App\YoutubeVideo;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer;

class VideosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Mailer $mailer)
    {
        $lastHarvest = Harvest::withTrashed()->orderBy('created_at', 'desc')->first();

        $accounts = GoogleAccount::get();

        $input = $request->all();
        $private = ($request->input('private', 'true') == 'true');
        $missingDate = ($request->input('missing_date', 'false') == 'true');

        return response()->view('recordings.index', [
            'lastHarvest' => $lastHarvest,
            'events' => YoutubeVideo::events($private, $missingDate),
            'accounts' => $accounts,

            'missingDate' => $missingDate,
            'urlWithMissingDate' => $this->urlWith($input, 'missing_date', 'true'),
            'urlWithoutMissingDate' => $this->urlWith($input, 'missing_date', 'false'),

            'private' => $private,
            'urlWithPrivate' => $this->urlWith($input, 'private', 'true'),
            'urlWithoutPrivate' => $this->urlWith($input, 'private', 'false'),
        ]);
    }

    /**
     * Display a listing of the resource as a feed.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function feed(Request $request)
    {
        $lastHarvest = Harvest::withTrashed()->orderBy('created_at', 'desc')->first();

        $limit = intval($request->input('limit', 10));

        $events = [];
        $n = 0;
        foreach (YoutubeVideo::events(false, false) as $gid => $event) {
            if (!count($event['recordings'])) continue;
            if ($event['recordings'][0]->upcoming()) continue;
            if (is_null($event['vortexEvent'])) continue;
            if (!$event['publicVideos']) continue;
            // if (!$event->account->id =!=) continue;

            $events[$gid] = $event;
            $n++;
            if ($n >= $limit) break;
        }

        return response()
            ->view('recordings.feed', [
                'events' => $events,
            ])
            ->header('Content-Type', 'application/xml')
            ;
    }

    /**
     * Hide a resource.
     *
     * @param Request $request
     * @param         $id
     * @return \Illuminate\Http\Response
     */
    public function hide(Request $request, $id)
    {
        $rec = YoutubeVideo::find($id);
        $rec->delete();

        $request->session()->flash('status', '«' . $rec->yt('title') . '» ble skjult');
        return redirect()->back();
    }

}
