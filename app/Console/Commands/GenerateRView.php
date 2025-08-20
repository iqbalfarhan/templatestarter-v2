<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateRView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:rview {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate React view structure under resources/js/pages/{name}';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = strtolower($this->argument('name'));
        $Name = Str::studly($name); // Capitalized (Project, Snippet, dsb)
        $basePath = resource_path("js/pages/{$name}");

        // Struktur file
        $files = [
            "index.tsx" => "stubs/react-stubs/index.stub",
            "show.tsx" => "stubs/react-stubs/show.stub",
            "components/{$name}-delete-dialog.tsx" => "stubs/react-stubs/delete-dialog.stub",
            "components/{$name}-filter-sheet.tsx" => "stubs/react-stubs/filter-sheet.stub",
            "components/{$name}-form-sheet.tsx" => "stubs/react-stubs/form-sheet.stub",
            "components/{$name}-item-card.tsx" => "stubs/react-stubs/item-card.stub",
            "../../types/{$name}.d.ts" => "stubs/react-stubs/type.stub",
        ];

        foreach ($files as $file => $stub) {
            $filePath = $basePath . '/' . $file;
            $dir = dirname($filePath);

            // Pastikan folder ada
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
                $this->info("Created directory: {$dir}");
            }

            // Generate file dari stub
            if (!File::exists($filePath)) {
                $stubPath = base_path($stub);

                if (File::exists($stubPath)) {
                    $content = File::get($stubPath);
                    // Replace placeholder
                    $content = str_replace(
                        ['{{ name }}', '{{ Name }}', '{{ names }}', '{{ Names }}'],
                        [$name, $Name, Str::plural($name), Str::pluralStudly($Name)],
                        $content
                    );
                } else {
                    $content = "// {$Name} - " . basename($filePath);
                }

                File::put($filePath, $content);
                $this->info("Created file: {$filePath}");
            } else {
                $this->warn("File already exists: {$filePath}");
            }
        }

        $this->info("React view for '{$name}' generated successfully!");
    }
}
