<?php

namespace App\Jobs;

use App\Event;
use App\Recording;
use Carbon\Carbon;
use cebe\markdown\GithubMarkdown;
use Illuminate\Contracts\Bus\SelfHandling;
use Zend\Feed\Writer\Feed;

class GenerateFeedJob extends Job implements SelfHandling
{
    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    // Inspirasjon: Se http://feeds.feedburner.com/TEDTalks_video
    public function handle()
    {
        $channelTitle = 'Realfagsbiblioteket';
        $channelShortDescription = 'Videoer om vitenskap';
        $channelDescription = 'Vitenskap formidlet fra scenen i Realfagsbiblioteket. Realfagsbiblioteket er Norges største fag- og forskningsbibliotek innen fagområdene fagområdene fysikk, astrofysikk, biofag, farmasi, geofag, kjemi, informatikk og matematikk.';

        $channelUrl = 'https://www.ub.uio.no/om/aktuelt/arrangementer/ureal/';
        $channelAuthor = 'Realfagsbiblioteket';
        $channelAuthorEmail = 'realfagsbiblioteket@ub.uio.no';
        $channelImage = 'http://titan.uio.no/sites/default/files/thumbnails/image/realfagsbiblioteket_topp_titan.png';

        $parser = new GithubMarkdown();

        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->startDocument('1.0','UTF-8');
        $writer->setIndent(true);
        $writer->setIndentString('  ');

        $writer->startElement('rss');
        $writer->writeAttribute('version', '2.0');
        $writer->writeAttribute('xmlns:media', 'http://search.yahoo.com/mrss/');
        $writer->writeAttribute('xmlns:atom', 'http://www.w3.org/2005/Atom');
        $writer->writeAttribute('xmlns:dct', 'http://dublincore.org/documents/2012/06/14/dcmi-terms/');
        $writer->writeAttribute('xmlns:itunes', 'http://www.itunes.com/dtds/podcast-1.0.dtd');

        $writer->startElement('channel');

            $writer->writeElement('title', $channelTitle);
            $writer->writeElement('link', $channelUrl);
            $writer->writeElement('description', $channelDescription);
            $writer->writeElement('itunes:subtitle', $channelShortDescription);
            $writer->writeElement('itunes:author', $channelAuthor);
            $writer->writeElement('itunes:summary', $channelDescription);

            $writer->startElement('itunes:owner');
                $writer->writeAttribute('itunes:name', $channelAuthor);
                $writer->writeAttribute('itunes:email', $channelAuthorEmail);
                $writer->writeAttribute('href', 'https://www.ub.uio.no/om/aktuelt/arrangementer/ureal/feed-til-titan.xml');
            $writer->endElement();

            $writer->startElement('itunes:image');
                $writer->writeAttribute('href', $channelImage);
            $writer->endElement();

            $writer->startElement('atom:link');
                $writer->writeAttribute('rel', 'self');
                $writer->writeAttribute('type', 'application/rss+xml');
                $writer->writeAttribute('href', 'https://www.ub.uio.no/om/aktuelt/arrangementer/ureal/feed-til-titan.xml');
            $writer->endElement();

            $lastModified = Carbon::createFromDate(2000, 1, 1);

            foreach (Recording::with('presentation', 'presentation.event')->orderBy('recorded_at', 'desc')->get() as $video) {
                if (!$video->presentation) {
                    continue;
                }
                $event = $video->presentation->event;
                if (count($event->presentations) != 1) {
                    continue;
                }

                $body = $parser->parse($event->intro);
                $body = str_replace('<hr />', '', $body);

                $primaryResource = null;
                foreach ($event->resources as $resource) {
                    if ($resource->filetype == 'image') {
                        $primaryResource = $resource;
                    }
                }

                $mp4Url = null;
                $mp4filesize = 0;

                $writer->startElement('item');
                    $writer->startElement('guid');
                        $writer->writeAttribute('isPermaLink', 'false');
                        $writer->writeRaw($event->uuid);
                    $writer->endElement();

                    //$event->start_date . ' ' . $video->presentation->start_time
                    $writer->writeElement('pubDate', $video->presentation->getStartDateTime()->toRssString());
                    $writer->writeElement('dct:created', $video->presentation->getStartDateTime()->toIso8601String());
                    $writer->writeElement('dct:modified', $event->updated_at->toIso8601String());
                    $writer->writeElement('dct:language', $video->language);
                    $writer->writeElement('title', $event->title);
                    // $writer->writeElement('recordDate', $video->recorded_at);
                    // $writer->writeElement('bodyLength', mb_strlen($body));

                    $writer->startElement('description');
                        $writer->writeCData($body);
                    $writer->endElement();

                    $content = '<iframe width="560" height="315" src="https://www.youtube.com/embed/' . $video->youtube_id . '" frameborder="0" allowfullscreen></iframe>';
                    $writer->startElement('content');
                        $writer->writeCData($content);
                    $writer->endElement();

                    $writer->startElement('media:embed');  // ???
                      $writer->writeAttribute('url', $video->youtubeLink('embed'));
                    $writer->endElement();

                    if (!is_null($mp4Url)) {
                        $writer->startElement('enclosure');
                        $writer->writeAttribute('url', $mp4Url);
                        $writer->writeAttribute('length', $mp4filesize);
                        $writer->writeAttribute('type', 'video/mp4');
                    }

                    if (!is_null($primaryResource)) {
                        $writer->startElement('media:content');
                            $writer->writeAttribute('url', $primaryResource->url('webdav'));
                            $writer->writeAttribute('width', $primaryResource->width);
                            $writer->writeAttribute('height', $primaryResource->height);
                            $writer->writeAttribute('type', 'image/jpeg');
                            // $writer->writeElement('attribution', $primaryResource->attribution);
                            // Se: http://www.rssboard.org/media-rss#media-license
                            //$writer->writeElement('media:license', $primaryResource->license);
                        $writer->endElement();
                    }

                    if ($event->updated_at > $lastModified) {
                        $lastModified = $event->updated_at;
                    }
                $writer->endElement();
            }

        $writer->writeElement('dct:modified', $lastModified->toIso8601String());
        $writer->writeElement('lastBuildDate', $lastModified->toIso8601String());

        $writer->endElement();

        $writer->endDocument();
        echo $writer->outputMemory();
    }

}
