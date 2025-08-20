<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateRModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:rmodel {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Laravel model + React view secara berurutan (amodel + rview)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = strtolower($this->argument('name')); // article
        $Name = Str::studly($this->argument('name')); // Article

        $this->info("🚀 Running generate:amodel {$Name} ...");
        $this->call('generate:amodel', [
            'name' => $Name,
        ]);

        $this->info("🎨 Running generate:rview {$name} ...");
        $this->call('generate:rview', [
            'name' => $name,
        ]);

        $this->info("✅ Done! {$Name} model + React view generated successfully!");
    }
}
