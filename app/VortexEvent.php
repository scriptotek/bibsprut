<?php

namespace App;

use App\Exceptions\ScrapeException;
use Carbon\Carbon;
use Goutte\Client as GoutteClient;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Symfony\Component\DomCrawler\Crawler;

class VortexEvent extends Model
{

    use SoftDeletes, HasStartEndTime;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['url'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['start_time', 'end_time', 'deleted_at'];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'organizers' => 'array',
        'tags' => 'array',
    ];

    /**
     * Get the presentations from this event.
     */
    public function recordings()
    {
        return $this->hasMany('App\YoutubeVideo')
            ->orderBy('start_time', 'asc');
    }

    public function scrape()
    {
        if (!empty($this->url) && empty($this->text)) {
            $client = new GoutteClient();
            $crawler = $client->request('GET', $this->url);
            $response = $client->getResponse();
            if ($response->getStatus() != 200) {
                throw new ScrapeException('Could not get URL ' . $this->url);
            }

            $crawler->filter('.vevent .dtstart')->each(function (Crawler $node) {
                $this->start_time = Carbon::parse($node->attr('title'));
            });

            if (!$this->start_time) {
                throw new ScrapeException('Not a valid Vortex event page: ' . $this->url);
            }

            $crawler->filter('#vrtx-content h1')->each(function (Crawler $node) {
                $this->title = trim($node->text());
            });

            $crawler->filter('.vrtx-introduction')->each(function (Crawler $node) {
                $this->introduction = $node->text();
            });

            $this->text = implode('\n\n', $crawler->filter('#vrtx-main-content > p')->each(function (Crawler $node) {
                // echo $node->text();
                return $node->text();
            }));

            $crawler->filter('.vevent .dtend')->each(function (Crawler $node) {
                $this->end_time = Carbon::parse($node->attr('title'));
            }); // 2017-02-02T09:30:00+01:00

            $crawler->filter('.vevent .location a')->each(function (Crawler $node) {
                $this->location = $node->text();
                $this->location_map_url = $node->attr('href');
            });

            $this->organizers = $crawler->filter('.vrtx-event-organizers .organizer')->each(function (Crawler $node) {
                return $node->text();
            }); // 2017-02-02T09:30:00+01:00

            $this->tags = $crawler->filter('.vrtx-tags a')->each(function (Crawler $node) {
                return $node->text();
            }); // 2017-02-02T09:30:00+01:00

        }
    }

}
