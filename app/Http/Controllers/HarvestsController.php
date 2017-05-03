<?php

namespace App\Http\Controllers;

use App\Jobs\YoutubeHarvestJob;
use Illuminate\Http\Request;

class HarvestsController extends Controller
{
    public function harvest(Request $request)
    {
        dispatch(
            new YoutubeHarvestJob()
        );

        $request->session()->flash('status', 'Oppdatering startet');

        return redirect()->back();
    }
}
