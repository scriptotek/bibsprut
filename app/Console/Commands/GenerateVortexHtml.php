<?php

namespace App\Console\Commands;

use App\Jobs\GenerateVortexHtmlJob;
use Illuminate\Console\Command;

class GenerateVortexHtml extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:vortex';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Vortex HTML.';

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
        dispatch(
            new GenerateVortexHtmlJob()
        );
    }
}
