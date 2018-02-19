<?php

namespace App\Http\Controllers;

use App\Jobs\YoutubeHarvestJob;
use App\Jobs\GenerateVortexHtmlJob;
use Illuminate\Http\Request;

class HarvestsController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:edit');
    }

    public function harvest(Request $request)
    {
        YoutubeHarvestJob::withChain([
            new GenerateVortexHtmlJob,
        ])->dispatch();

        $request->session()->flash('status', 'Oppdatering startet');

        return redirect()->back();
    }

    public function log(Request $request)
    {
        $config = new \IcyApril\Tail\Config(\storage_path('logs/worker.log'));
        $config->setLines(2000);
        $tail = new \IcyApril\Tail\Tail($config);

        return view('harvests.log', [
            'data' => $tail->getTail(),
        ]);
    }
}
