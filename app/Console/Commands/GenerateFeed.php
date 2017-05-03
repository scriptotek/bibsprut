<?php

namespace App\Console\Commands;

use App\Jobs\GenerateFeedJob;
use Illuminate\Console\Command;

class GenerateFeed extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:feed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate atom feed.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$this->info('Generating feed');
        dispatch(
            new GenerateFeedJob()
        );
        //$this->info('Done');
    }
}
