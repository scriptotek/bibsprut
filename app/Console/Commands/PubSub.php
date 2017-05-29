<?php

namespace App\Console\Commands;

use App\GoogleAccount;
use Illuminate\Console\Command;
use Pubsubhubbub\Subscriber\Subscriber;

class PubSub extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pubsub:subscribe
        {--unsubscribe : Unsubscribe rather than subscribe}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to or unsubscribe from PubSub notifications.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $hub_url      = 'https://pubsubhubbub.appspot.com/subscribe';
        $callback_url = action('PubSubController@process');

        // create a new subscriber
        $subscriber = new Subscriber($hub_url, $callback_url);

        foreach (GoogleAccount::get() as $acc) {
            $feed = "https://www.youtube.com/xml/feeds/videos.xml?channel_id={$acc->channel['id']}";

            if ($this->option('unsubscribe')) {
                if ($subscriber->unsubscribe($feed) === false) {
                    \Log::error("Failed to unsubscribe from $feed");
                } else {
                    \Log::info("Unsubscribed from $feed");
                }
            } else {
                if ($subscriber->subscribe($feed) === false) {
                    \Log::error("Failed to subscribe to $feed");
                } else {
                    \Log::info("Subscribed to $feed");
                }
            }
        }
    }
}
