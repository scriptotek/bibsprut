<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\GoogleAccount;
use App\Harvest;
use App\YoutubeVideo;
use Illuminate\Http\Request;

class VideosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $lastHarvest = Harvest::orderBy('created_at', 'desc')->first();

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
