<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;

class GenerateAModel extends Command
{
    protected $signature = 'generate:amodel {name} {--sd|softDelete} {--fields=}';
    protected $description = 'Generate model, factory, seeder, requests, and migration for a given name';

    public function handle()
    {
        $Name = Str::studly($this->argument('name')); // ex: User
        $name = Str::camel($Name);                   // ex: user
        $Names = Str::pluralStudly($Name);           // ex: Users
        $names = Str::camel($Names);                 // ex: users
        $tableName = Str::snake($Names);             // ex: users

        $softDelete = $this->option('softDelete');
        $fieldsOption = $this->option('fields'); // ex: "title:string, body:text , is_active:boolean"
        $fields = [];

        if ($fieldsOption) {
            foreach (explode(',', $fieldsOption) as $field) {
                // trim setiap field dulu
                $field = trim($field);

                // pecah name:type, fallback ke 'string' kalau ga ada type
                [$fname, $ftype] = array_pad(explode(':', $field), 2, 'string');

                // trim lagi supaya bersih
                $fname = trim($fname);
                $ftype = trim($ftype);

                $fields[$fname] = $ftype;
            }
        }


        // Path Laravel bawaan
        $paths = [
            "app/Models/{$Name}.php" => "stubs/php-stubs/model.stub",
            "database/factories/{$Name}Factory.php" => "stubs/php-stubs/factory.stub",
            "database/seeders/{$Name}Seeder.php" => "stubs/php-stubs/seeder.stub",
            "app/Http/Requests/Store{$Name}Request.php" => "stubs/php-stubs/store-request.stub",
            "app/Http/Requests/Update{$Name}Request.php" => "stubs/php-stubs/update-request.stub",
            "app/Http/Requests/BulkUpdate{$Name}Request.php" => "stubs/php-stubs/bulk-update-request.stub",
            "app/Http/Requests/BulkDelete{$Name}Request.php" => "stubs/php-stubs/bulk-delete-request.stub",
            "app/Http/Controllers/{$Name}Controller.php" => "stubs/php-stubs/controller.stub",
        ];

        foreach ($paths as $file => $stub) {
            $this->makeFromStub($file, $stub, [
                '{{ name }}'  => $name,
                '{{ Name }}'  => $Name,
                '{{ names }}' => $names,
                '{{ Names }}' => $Names,
                '{{ table }}' => $tableName,
                '{{ softDeleteImport }}' => $softDelete ? "use Illuminate\\Database\\Eloquent\\SoftDeletes;\n" : "",
                '{{ softDeleteTrait }}'  => $softDelete ? "use SoftDeletes;\n" : "",
                '{{ softDeleteMethods }}'  => $softDelete ? $this->generateSoftDeleteMethods($softDelete, $Name, $name, $names) : "",
                '{{ fillable }}' => $this->generateFillable($fields),
                '{{ factory }}' => $this->generateFactory($fields),
                '{{ request }}' => $this->generateRequest($fields),
                '{{ migrationFields }}' => $this->generateMigrationFields($fields, $softDelete),
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
            '{{ softDeleteColumn }}' => $softDelete ? "\$table->softDeletes();\n" : "",
            '{{ migrationFields }}' => $this->generateMigrationFields($fields, $softDelete),
        ]);

        $this->info("✅ {$Name} model + related files generated successfully!");
        if ($softDelete) {
            $this->info("🗑️  SoftDeletes enabled for {$name}");
        }

        // Add router to web.php
        $this->addRoute($softDelete,$name, $Name);

        // Generate permissions
        $this->generatePermissions($softDelete, $name);
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

    protected function addRoute(bool $softDelete, $name, $Name)
    {
        $webPath = base_path('routes/web.php');

        $useLine   = "use App\\Http\\Controllers\\{$Name}Controller;\n";
        $routeLine  = "    Route::put('" . Str::camel($name) . "/bulk', [{$Name}Controller::class, 'bulkUpdate'])->name('" . Str::camel($name) . ".bulk.update');\n";
        $routeLine .= "    Route::delete('" . Str::camel($name) . "/bulk', [{$Name}Controller::class, 'bulkDelete'])->name('" . Str::camel($name) . ".bulk.destroy');\n";

        // Tambahin routes kalau pakai softDelete
        if ($softDelete) {
            $routeLine .= "    Route::get('" . Str::camel($name) . "/archived', [{$Name}Controller::class, 'archived'])->name('" . Str::camel($name) . ".archived');\n";
            $routeLine .= "    Route::put('" . Str::camel($name) . "/{" . Str::camel($name) . "}/restore', [{$Name}Controller::class, 'restore'])->name('" . Str::camel($name) . ".restore');\n";
            $routeLine .= "    Route::delete('" . Str::camel($name) . "/{" . Str::camel($name) . "}/force-delete', [{$Name}Controller::class, 'forceDelete'])->name('" . Str::camel($name) . ".force-delete');\n";
        }

        $routeLine .= "    Route::apiResource('" . Str::camel($name) . "', {$Name}Controller::class);\n";


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

    protected function generateFillable(array $fields): string
    {
        if (empty($fields)) return '';
        $items = array_map(fn($f) => "'$f'", array_keys($fields));
        return implode(",\n        ", $items);
    }

    protected function generateFactory(array $fields): string
    {
        $fakerMap = [
            'string' => 'fake()->sentence()',
            'text' => 'fake()->paragraph()',
            'boolean' => 'fake()->boolean()',
            'integer' => 'fake()->randomNumber()',
            'datetime' => 'fake()->dateTime()',
        ];

        $out = [];
        if (count($fields) == 0) $fields = ['name' => 'string'];
        foreach ($fields as $f => $t) {
            $faker = $fakerMap[$t] ?? 'fake()->word()';
            $out[] = "'$f' => $faker,";
        }
        return implode("\n            ", $out);
    }

    protected function generateMigrationFields(array $fields, bool $softDelete): string
    {
        $out = [];
        foreach ($fields as $f => $t) {
            $out[] = "\$table->$t('$f');";
        }
        if(count($fields) == 0) $out[] = "\$table->string('name');";
        if ($softDelete) $out[] = "\$table->softDeletes();";
        $out[] = "\$table->timestamps();";
        return implode("\n            ", $out);
    }

    protected function generateRequest(array $fields): string
    {
        $fieldMap = [
            'string' => "'required|string|max:255'",
            'text' => "'required|string'",
            'boolean' => "'required|boolean'",
            'integer' => "'required|numeric'",
            'datetime' => "'required|string'",
        ];

        $out = [];
        if (count($fields) == 0) $fields = ['name' => 'string'];
        foreach ($fields as $f => $t) {
            $field = $fieldMap[$t] ?? 'nullable';
            $out[] = "'$f' => $field,";
        }
        return implode("\n            ", $out);
    }

    protected function generateSoftDeleteMethods(bool $softDelete, string $Name, string $name, string $names): string
    {
        if (!$softDelete) return '';

        return <<<EOT
            /**
             * View archived resource from storage.
             */
            public function archived()
            {
                return Inertia::render('{$name}/archived', [
                    '{$names}' => {$Name}::onlyTrashed()->get(),
                ]);
            }

            /**
             * Restore the specified resource from storage.
             */
            public function restore(\$id)
            {
                \$model = {$Name}::onlyTrashed()->findOrFail(\$id);
                \$model->restore();
            }

            /**
             * Force delete the specified resource from storage.
             */
            public function forceDelete(\$id)
            {
                \$model = {$Name}::onlyTrashed()->findOrFail(\$id);
                \$model->forceDelete();
            }
        EOT;
    }

    protected function generatePermissions(bool $softDelete, string $name)
    {
        $permissions = [
            "menu {$name}",
            "index {$name}",
            "show {$name}",
            "create {$name}",
            "update {$name}",
            "delete {$name}",
        ];

        if ($softDelete) {
            $permissions[] = "archived {$name}";
            $permissions[] = "restore {$name}";
            $permissions[] = "force delete {$name}";
        }

        foreach ($permissions as $permit) {
            Permission::updateOrCreate([
                'group' => $name,
                'name' => $permit,
            ]);
        }

        $this->info("🔑 Permissions created: " . implode(', ', $permissions));
    }

}
