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
        $Name = Str::studly($this->argument('name')); // ex: User
        $name = Str::camel($Name);                   // ex: user
        $Names = Str::pluralStudly($Name);           // ex: Users
        $names = Str::camel($Names);                 // ex: users
        $tableName = Str::snake($Names);             // ex: users

        // Path Laravel bawaan
        $paths = [
            "app/Models/{$Name}.php" => "stubs/php-stubs/model.stub",
            "database/factories/{$Name}Factory.php" => "stubs/php-stubs/factory.stub",
            "database/seeders/{$Name}Seeder.php" => "stubs/php-stubs/seeder.stub",
            "app/Http/Requests/Store{$Name}Request.php" => "stubs/php-stubs/store-request.stub",
            "app/Http/Requests/Update{$Name}Request.php" => "stubs/php-stubs/update-request.stub",
            "app/Http/Controllers/{$Name}Controller.php" => "stubs/php-stubs/controller.stub",
        ];

        foreach ($paths as $file => $stub) {
            $this->makeFromStub($file, $stub, [
                '{{ name }}'  => $name,
                '{{ Name }}'  => $Name,
                '{{ names }}' => $names,
                '{{ Names }}' => $Names,
                '{{ table }}' => $tableName,
            ]);
        }

        // Migration
        $migrationName = date('Y_m_d_His') . "_create_{$tableName}_table.php";
        $migrationPath = database_path("migrations/{$migrationName}");
        $this->makeFromStub($migrationPath, "stubs/php-stubs/migration.stub", [
            '{{ name }}'  => $name,
            '{{ Name }}'  => $Name,
            '{{ Names }}' => $Names,
            '{{ table }}' => $tableName,
        ]);

        $this->info("✅ {$Name} model + related files generated successfully!");

        $this->addRoute($name, $Name);
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

    protected function addRoute($name, $Name)
    {
        $webPath = base_path('routes/web.php');

        $useLine   = "use App\\Http\\Controllers\\{$Name}Controller;\n";
        $routeLine = "    Route::apiResource('" . Str::camel($name) . "', {$Name}Controller::class);\n";

        if (File::exists($webPath)) {
            $content = File::get($webPath);

            // ✅ Tambahin use kalau belum ada
            if (!Str::contains($content, "use App\\Http\\Controllers\\{$Name}Controller;")) {
                // cari posisi terakhir "use " terus sisipin di bawahnya
                if (preg_match_all('/^use\s.+;$/m', $content, $matches, PREG_OFFSET_CAPTURE)) {
                    $lastUse = end($matches[0]);
                    $pos = $lastUse[1] + strlen($lastUse[0]);
                    $content = substr($content, 0, $pos) . "\n{$useLine}" . substr($content, $pos);
                } else {
                    // fallback kalau gak ada use
                    $content = "<?php\n\n{$useLine}" . $content;
                }
                File::put($webPath, $content);
                $this->info("📌 Added import: {$useLine}");
            }

            // ✅ Tambahin route ke dalam middleware group
            if (Str::contains($content, "Route::middleware(['auth', 'verified'])->group(function () {")) {
                // sisipkan sebelum "});"
                $content = preg_replace(
                    "/(Route::middleware\(\['auth', 'verified'\]\)->group\(function\s*\(\)\s*{\n)([\s\S]*?)(^\s*}\);)/m",
                    "$1$2$routeLine$3",
                    $content,
                    1,
                    $count
                );

                if ($count > 0) {
                    File::put($webPath, $content);
                    $this->info("🌐 Added route to middleware group: {$routeLine}");
                } else {
                    $this->warn("⚠️ Could not insert route inside middleware group, fallback to bottom.");
                    if (!Str::contains($content, $routeLine)) {
                        File::append($webPath, $routeLine);
                    }
                }
            } else {
                // fallback kalau ga ada group
                if (!Str::contains($content, $routeLine)) {
                    File::append($webPath, $routeLine);
                    $this->info("🌐 Added route to bottom: {$routeLine}");
                }
            }
        }
    }

}
