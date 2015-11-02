<?php

namespace App\Console\Commands;

use App\Jobs\GenerateFeedJob;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesCommands;

class GenerateFeed extends Command
{
    use DispatchesCommands;

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
        $this->info('Generating feed');
        $this->dispatch(
            new GenerateFeedJob()
        );
        $this->info('Done');
    }
}
