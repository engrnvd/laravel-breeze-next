<?php

namespace Naveed\BreezeNext\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'breeze-next:setup')]
class SetUpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'breeze-next:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets up the project to act as a stateless api for a backend NextJs application';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info("Setting up...");
        return 0;
    }
}
