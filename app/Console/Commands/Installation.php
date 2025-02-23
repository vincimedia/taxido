<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Installation extends Command
{
    protected $hidden = false;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'web:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('If you have previously run this command or migrated tables, be advised that it will erase all of your data.');
        if ($this->confirm('Do you want to continue installation?')) {
            $this->info('Installing..');
            if ($this->confirm('Do you want to import dummy data?')) {
                $this->call('db:wipe');
                $this->info('Dropping all tables...');
                $this->info('Importing dummy data...');
                $this->call('dummy:import');
                $this->info('Dummy Data Imported Successfully!');
            } else {

                $this->info('Migration is being run to build tables...');
                $this->call('migrate:fresh');
                $this->info('The seeder is being used for Generating the Administrator Credentials.');
                $this->call('db:seed');
                $this->call('module:seed', ['--all' => true]);
                $this->info('Seed completed successfully!');
            }

            $this->info('');
            $this->info('Installed Successfully.');
        }
    }
}
