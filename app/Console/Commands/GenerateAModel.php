<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateAModel extends Command
{
    protected $signature = 'generate:amodel {name}';
    protected $description = 'Generate model, factory, seeder, requests, and migration for a given name';

    public function handle()
    {
        $name = Str::studly($this->argument('name')); // ex: Post, User
        $namePlural = Str::pluralStudly($name);      // ex: Posts, Users
        $tableName = Str::snake(Str::plural($name)); // ex: posts, users

        // Path Laravel bawaan
        $paths = [
            "app/Models/{$name}.php" => "stubs/php-stubs/model.stub",
            "database/factories/{$name}Factory.php" => "stubs/php-stubs/factory.stub",
            "database/seeders/{$name}Seeder.php" => "stubs/php-stubs/seeder.stub",
            "app/Http/Requests/Store{$name}Request.php" => "stubs/php-stubs/store-request.stub",
            "app/Http/Requests/Update{$name}Request.php" => "stubs/php-stubs/update-request.stub",
            "app/Http/Controllers/{$name}Controller.php" => "stubs/php-stubs/controller.stub",
        ];

        foreach ($paths as $file => $stub) {
            $this->makeFromStub($file, $stub, [
                '{{ name }}' => Str::camel($name),
                '{{ Name }}' => $name,
                '{{ names }}' => Str::camel(Str::plural($name)),// ex: users
                '{{ Names }}' => $namePlural,
                '{{ table }}' => $tableName,
            ]);
        }

        // 🔹 Migration khusus (karena harus pakai timestamp)
        $migrationName = date('Y_m_d_His') . "_create_{$tableName}_table.php";
        $migrationPath = database_path("migrations/{$migrationName}");
        $this->makeFromStub($migrationPath, "stubs/php-stubs/migration.stub", [
            '{{ name }}' => Str::camel($name),
            '{{ Name }}' => $name,
            '{{ Names }}' => $namePlural,
            '{{ table }}' => $tableName,
        ]);

        $this->info("✅ {$name} model + related files generated successfully!");
    }

    protected function makeFromStub($filePath, $stubPath, $replacements)
    {
        $dir = dirname($filePath);
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
            $this->info("📂 Created directory: {$dir}");
        }

        if (!File::exists($filePath)) {
            $stubFullPath = base_path($stubPath);
            $content = File::exists($stubFullPath) ? File::get($stubFullPath) : "// Stub not found";
            $content = str_replace(array_keys($replacements), array_values($replacements), $content);

            File::put($filePath, $content);
            $this->info("📝 Created file: {$filePath}");
        } else {
            $this->warn("⚠️ File already exists: {$filePath}");
        }
    }
}
