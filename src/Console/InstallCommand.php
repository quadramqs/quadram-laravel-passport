<?php

namespace Quadram\LaravelPassport\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quadram:laravel-passport-install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the commands necessary to prepare Passport for use';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->call('passport:install', ['--force' => true]);

        $this->info('Passsport installed');
    }
}
