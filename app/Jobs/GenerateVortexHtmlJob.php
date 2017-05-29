<?php

namespace App\Jobs;

use App\YoutubeVideo;
use App\WebdavClient;
use RuntimeException;

class GenerateVortexHtmlJob extends Job
{
    // Inspirasjon: Se http://feeds.feedburner.com/TEDTalks_video

    protected $url = 'https://www.ub.uio.no/kurs-arrangement/live/index.html';

    protected $stdout = false; // Print to stdout rather than saving to Vortex

    protected $maxUpcoming = 4;  // Max number of upcoming events to show

    protected $templates = [

        'main' =>

'<h2>Neste sending</h2>
{% if nextEvent %}
<div class="vrtx-title">
    <a class="vrtx-title summary" href="{{ nextEvent.vortex.url }}">{{ nextEvent.title }}</a>
</div>
<div class="time-and-place">{{ nextEvent.when | datetimeformat("%e. %B %Y") }},
    <a href="{{ nextEvent.vortex.location_map_url }}">{{ nextEvent.vortex.location }}</a>
</div>
<div>
    <iframe width="560" height="315" src="{{ nextEvent.embedLink }}" frameborder="0" allowfullscreen></iframe>
</div>
{% else %}
<em>Ingen planlagte</em>
{% endif %}

<h2>Kommende sendinger</h2>
<ul>
{% for event in upcomingEvents %}
    <li>
        <div class="vrtx-title">
            <a class="vrtx-title summary" href="{{ event.vortex.url }}">{{ event.title }}</a>
        </div>
        <div class="time-and-place">{{ event.when | datetimeformat("%e. %B %Y") }},
        <a href="{{ event.vortex.location_map_url }}">{{ event.vortex.location }}</a>
        </div>
    </li>
{% endfor %}
</ul>

<h2>Tidligere sendinger</h2>

<ul>
{% for event in pastEvents %}
{% if event.playlist %}{{ include("pastEventPlaylist") }}
{% elseif event.publicVideos > 1 %}{{ include("pastEventMultiple") }}
{% else %}{{ include("pastEvent") }}
{% endif %}
{% endfor %}
</ul>
',

        // Past event with single recording
        'pastEvent' =>

'<li>
    {{ event.title }}
    ({{ event.when | dateformat("%e. %B %Y") }}) :
    <a href="{{ event.youtubeLink }}">Opptak</a>
{%if event.vortex.url %}    / <a href="{{ event.vortex.url }}">arrangement</a>
{% endif %}
</li>',

        // Past event with multiple recordings, but no playlist
        'pastEventMultiple' =>

'{% for rec in event.recordings %}
<li>
    {{ rec.title }}
    ({{ rec.when | dateformat("%e. %B %Y") }}) :
    <a href="{{ rec.youtubeLink }}">Opptak</a>
{%if rec.vortex.url %}    / <a href="{{ rec.vortex.url }}">arrangement</a>
{% endif %}
</li>
{% endfor %}',

        // Past with multiple recordings and playlist
        'pastEventPlaylist' =>

'<li>
    {{ event.title }}
    ({{ event.when | dateformat("%e. %B %Y") }}) :
    <a href="{{ event.youtubeLink }}">Opptak (spilleliste)</a>
{%if rec.vortex.url %}    / <a href="{{ rec.vortex.url }}">arrangement</a>
{% endif %}
</li>',

    ];

    public function __construct($stdout)
    {
        $this->stdout = $stdout;
    }

    public function formatRecording($rec)
    {
        return [
            'title' => $rec->yt('title'),
            'when' => $rec,
            'vortex' => $rec->vortexEvent,
            'youtube' => $rec->youtube_meta,
            'youtubeLink' => $rec->youtubeLink(),
            'embedLink' => $rec->youtubeLink('embed'),
        ];
    }

    public function formatEvent($event)
    {
        $recording = $event['recordings'][0];

        if ($event['publicVideos'] > 1 && isset($event['playlist'])) {
            $youtubeLink = $event['playlist']->youtubeLink();
            $playlist = $event['playlist'];
        } else {
            $youtubeLink = $recording->youtubeLink();
            $playlist = null;
        }


        if (count($event['recordings']) > 1 && $recording->vortexEvent && !is_null($recording->vortexEvent->start_time)) {
            // For events with multiple recordings, we get the start and end time from the Vortex event,
            // since that covers the whole event.
            $when = $recording->vortexEvent;
        } else if ($recording->vortexEvent && (is_null($recording->start_time) || is_null($recording->end_time))) {
            // If start or end time is missing on the Youtube event, we also prefer the Vortex event.
            $when = $recording->vortexEvent;
        }  else {
            // Otherwise, we get start and end time from the YouTube event, since the Vortex event may cover
            // extra stuff which is not streamed (example: PhD Day, streaming: 14-15.30, vortex event: 11-18).
            $when = $recording;
        }

        if (count($event['recordings']) == 1) {
            // Use title from YouTube event
            $title = $recording->yt('title');
        } else {
            // Use title from Vortex event
            $title = $event['title'];
        }

        $recordings = array_map([$this, 'formatRecording'], $event['recordings']);

        return [
            'playlist' => $playlist,
            'publicVideos' => $event['publicVideos'],
            'title' => $title, // $recording->yt('title'),
            'when' => $when,
            'vortex' => $recording->vortexEvent,
            'youtube' => $recording->youtube_meta,
            'youtubeLink' => $youtubeLink,
            'embedLink' => $recording->youtubeLink('embed'),
            'recordings' => $recordings,
        ];
    }

    /**
     * @return string
     */
    protected function generateHtml()
    {
        setlocale(LC_TIME, 'no_NO');

        $args = [];
        $upcomingEvents = [];
        $pastEvents = [];

        foreach (YoutubeVideo::events(false, false) as $event) {
            if ($event['recordings'][0]->upcoming()) {
                $upcomingEvents[] = $event;
            } else {
                $pastEvents[] = $event;
            }
        }

        if (count($upcomingEvents)) {
            $event = array_pop($upcomingEvents);
            $args['nextEvent'] = $this->formatEvent($event);
        }

        $args['upcomingEvents'] = array_map(function ($event) {
            return $this->formatEvent($event);
        }, array_slice(array_reverse($upcomingEvents), 0, $this->maxUpcoming));

        $args['pastEvents'] = array_map(function ($event) {
            return $this->formatEvent($event);
        }, $pastEvents);

        $twig = new \Twig_Environment(new \Twig_Loader_Array($this->templates));

        $twig->addFilter(new \Twig_SimpleFilter('dateformat', function ($obj, array $options = array()) {
            if (is_null($obj)) {
                return '???';
            }

            return trim($obj->formatStartEndDate($options[0]));
        }, array('is_variadic' => true)));

        $twig->addFilter(new \Twig_SimpleFilter('datetimeformat', function ($obj, array $options = array()) {
            if (is_null($obj)) {
                return '???';
            }

            return trim($obj->formatStartEndDateTime($options[0]));
        }, array('is_variadic' => true)));

        $html = $twig->render('main', $args);

        return $html;
    }

    public function handle(WebdavClient $webdav)
    {
        $body = $webdav->get($this->url);
        $content = $body->properties->content;
        $spl = mb_split('<!--\s*Start:Blekkio\s*-->', $content);
        if (count($spl) != 2) {
            throw new RuntimeException('Did not find <!-- Start:Blekkio --> (or found multiple)');
        }
        $spl2 = mb_split('<!--\s*End:Blekkio\s*-->', $spl[1]);
        if (count($spl2) != 2) {
            throw new RuntimeException('Did not find <!-- End:Blekkio --> (or found multiple)');
        }

        $newContent = $this->generateHtml();

        if ($this->stdout) {
            echo $newContent . "\n";
            return;
        }

        $newContent = $spl[0] . '<!-- Start:Blekkio -->' . $newContent . '<!-- End:Blekkio -->' . $spl2[1];
        if ($body->properties->content != $newContent) {
            $body->properties->content = $newContent;

            $webdav->put($this->url, json_encode($body));
            \Log::info('Updated Vortex page.');
        } else {
            \Log::info('No need to update Vortex page, no changes.');
        }
    }

}
