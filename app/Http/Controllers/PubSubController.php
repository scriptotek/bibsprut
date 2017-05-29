<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Jobs\YoutubeHarvestJob;
use Danmichaelo\QuiteSimpleXMLElement\QuiteSimpleXMLElement;
use Illuminate\Http\Request;

class PubSubController extends Controller
{
    /**
     * Verify
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        \Log::info('[PubSubController] Responding to verify call with hub challenge: ' . $request->hub_challenge . ', lease seconds: ' . $request->hub_lease_seconds);

        return response($request->hub_challenge, 200);
    }
    /**
     * Process a notification
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function process(Request $request)
    {
        $content = $request->getContent();
        $xml = QuiteSimpleXMLElement::make($content, [
            'atom' => 'http://www.w3.org/2005/Atom',
            'yt' => 'http://www.youtube.com/xml/schemas/2015',
        ]);

        $videoId = $xml->text('atom:entry/yt:videoId');
        $channelId = $xml->text('atom:entry/yt:channelId');
        \Log::info("[PubSubController] Got push notification from YouTube: Video : {$videoId} at channel {$channelId} was changed.");

        dispatch(
            new YoutubeHarvestJob(false, [
                'videoId' => $videoId,
                'channelId' => $channelId,
            ])
        );

        return response('OK', 200);
    }
}
