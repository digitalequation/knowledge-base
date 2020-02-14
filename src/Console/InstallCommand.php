<?php

namespace DigitalEquation\KnowledgeBase\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'knowledge-base:install {--force : Force Knowledge Base to install even it has been already installed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Knowledge Base scaffolding into the application';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->comment('Publishing Knowledge Base Configuration...');
        $this->callSilent('vendor:publish', ['--tag' => 'knowledge-base-config']);

        $this->info('Knowledge Base scaffolding installed successfully.');
    }
}