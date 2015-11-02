<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Contracts\Bus\SelfHandling;
use Zend\Feed\Writer\Feed;

class GenerateFeedJob extends Job implements SelfHandling
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * Create the parent feed
         */
        $feed = new Feed;
        $feed->setTitle('Paddy\'s Blog');
        $feed->setLink('http://www.example.com');
        $feed->setFeedLink('http://www.example.com/atom', 'atom');
        $feed->addAuthor(array(
            'name'  => 'Paddy',
            'email' => 'paddy@example.com',
            'uri'   => 'http://www.example.com',
        ));
        $feed->setDateModified(time());
        $feed->addHub('http://pubsubhubbub.appspot.com/');

        /**
         * Add one or more entries. Note that entries must
         * be manually added once created.
         */
        $entry = $feed->createEntry();
        $entry->setTitle('All Your Base Are Belong To Us');
        $entry->setLink('http://www.example.com/all-your-base-are-belong-to-us');
        $entry->addAuthor(array(
            'name'  => 'Paddy',
            'email' => 'paddy@example.com',
            'uri'   => 'http://www.example.com',
        ));
        $entry->setDateModified(time());
        $entry->setDateCreated(time());
        $entry->setDescription('Exposing the difficultly of porting games to English.');
        $entry->setContent(
            'I am not writing the article. The example is long enough as is ;).'
        );

        $deleted = $feed->createTombstone();
        $deleted->setReference('http://www.example.com/all-your-base-are-belong-to-us');
        $deleted->setWhen(new \DateTime());
        $feed->addTombstone($deleted);

//        $entry->setLink('http://www.example.com/all-your-base-are-to-us');


        $feed->addEntry($entry);

        /**
         * Render the resulting feed to Atom 1.0 and assign to $out.
         * You can substitute "atom" with "rss" to generate an RSS 2.0 feed.
         */
        $out = $feed->export('atom');

        print $out;

    }
}
