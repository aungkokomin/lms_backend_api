<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\File;
use Illuminate\Console\Command;

class MakeRepositoryInterface extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository-interface {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Repository Interface';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $name = $this->argument('name');
        $path = app_path("Interfaces/{$name}.php");

        if (File::exists($path)) {
            $this->error("Interface {$name} already exists!");
            return;
        }

        File::ensureDirectoryExists(app_path('Interfaces'));

        File::put($path, $this->getStubContent($name));

        $this->info("Interface {$name} created successfully.");
    }

    protected function getStubContent($name)
    {
        return <<<PHP
        <?php

        namespace App\Interfaces;

        interface {$name}
        {
            // Add your Interfaces methods here

        }
        PHP;
    }
}
