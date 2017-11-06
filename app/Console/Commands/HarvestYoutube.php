<?php

namespace App\Console\Commands;

use App\Jobs\YoutubeHarvestJob;
use Illuminate\Console\Command;

class HarvestYoutube extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'harvest:youtube {--f|force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Harvest videos from Youtube.';

    /**
     * Create a new command instance.
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
        $this->info('Harvesting video metadata from Youtube');
        $force = $this->option('force');
        YoutubeHarvestJob::dispatch($force);
        $this->info('Done');
    }
}
