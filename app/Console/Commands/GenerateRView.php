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
    protected $signature = 'generate:rview {name} {--s|softDelete} {--fields=} {--m|media}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate React view structure under resources/js/pages/{name}';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = strtolower(strval($this->argument('name')));
        $Name = Str::studly($name);
        $Names = Str::plural($name);
        $basePath = rtrim(strval(config("template-starter.generated-react-files-path")), '/'). "/{$name}";

        $isSoftDelete = boolval($this->option('softDelete'));
        $withMedia = boolval($this->option('media'));

        $files = $this->buildFilesArray($name, $Name, $isSoftDelete, $withMedia);

        foreach ($files as $file => $stub) {
            $filePath = $basePath . '/' . $file;

            [$archivedButton, $uploadButton] = $this->buildButtons($isSoftDelete, $withMedia, $name, $Name);

            $replacements = [
                '{{ name }}' => $name,
                '{{ Name }}' => $Name,
                '{{ names }}' => $Names,
                '{{ Names }}' => Str::pluralStudly($Name),
                '{{ archivedButton }}' => $archivedButton,
                '{{ uploadButton }}' => $uploadButton,
            ];

            $this->makeFromStub($filePath, $stub, $replacements);
        }

        $parsedFields = $this->parseFieldsOption($this->option('fields'));
        [$typeLines, $imports] = $this->generateTypeLines($parsedFields, $withMedia);
        $importLines = $this->buildImportLines($imports, $withMedia);
        $this->updateTypeDefinition($name, $importLines, $typeLines);

        return self::SUCCESS;
    }

    protected function buildFilesArray(string $name, string $Name, bool $isSoftDelete, bool $withMedia): array
    {
        $files = [
            "index.tsx" => "resources/stubs/react-stubs/index.stub",
            "show.tsx" => "resources/stubs/react-stubs/show.stub",
            "components/{$name}-delete-dialog.tsx" => "resources/stubs/react-stubs/delete-dialog.stub",
            "components/{$name}-filter-sheet.tsx" => "resources/stubs/react-stubs/filter-sheet.stub",
            "components/{$name}-form-sheet.tsx" => "resources/stubs/react-stubs/form-sheet.stub",
            "components/{$name}-bulk-edit-sheet.tsx" => "resources/stubs/react-stubs/bulk-edit-sheet.stub",
            "components/{$name}-bulk-delete-dialog.tsx" => "resources/stubs/react-stubs/bulk-delete-dialog.stub",
            "components/{$name}-item-card.tsx" => "resources/stubs/react-stubs/item-card.stub",
            "../../types/{$name}.d.ts" => "resources/stubs/react-stubs/type.stub",
        ];

        if ($isSoftDelete) {
            $files["archived.tsx"] = "resources/stubs/react-stubs/archived.stub";
        }

        if ($withMedia) {
            $files["components/{$name}-upload-sheet.tsx"] = "resources/stubs/react-stubs/upload-sheet.stub";
        }

        return $files;
    }

    protected function makeFromStub(string $filePath, string $stubPath, array $replacements): void
    {
        $dir = dirname($filePath);
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
            $this->info("Created directory: {$dir}");
        }

        if (!File::exists($filePath)) {
            $stubFullPath = base_path($stubPath);
            if (File::exists($stubFullPath)) {
                $content = File::get($stubFullPath);
                $content = str_replace(array_keys($replacements), array_values($replacements), $content);
            } else {
                $content = "// " . ($replacements['{{ Name }}'] ?? 'Generated') . ' - ' . basename($filePath);
            }

            File::put($filePath, $content);
            $this->info("Created file: {$filePath}");
        } else {
            $this->warn("File already exists: {$filePath}");
        }
    }

    protected function buildButtons(bool $isSoftDelete, bool $withMedia, string $name, string $Name): array
    {
        $archivedButton = $isSoftDelete
            ? <<<EOT
                        <Button variant={'destructive'} asChild>
                            <Link href={route('{$name}.archived')}>
                                <FolderArchive />
                                Archived
                            </Link>
                        </Button>
                        EOT
            : '';

        $uploadButton = $withMedia
            ? <<<EOT
                        <{$Name}UploadMediaSheet {$name}={{$name}}>
                            <Button variant={'ghost'} size={'icon'}>
                                <Image />
                            </Button>
                        </{$Name}UploadMediaSheet>
                        EOT
            : '';

        return [$archivedButton, $uploadButton];
    }

    protected function parseFieldsOption($fieldsOption): array
    {
        if (!$fieldsOption) {
            return [['name', 'string']];
        }

        $raw = array_map('trim', explode(',', strval($fieldsOption)));
        $parsed = [];
        foreach ($raw as $field) {
            [$fieldName, $fieldType] = array_pad(explode(':', $field), 2, 'string');
            $parsed[] = [trim($fieldName), strtolower(trim($fieldType))];
        }
        return $parsed;
    }

    protected function generateTypeLines(array $parsedFields, bool $withMedia): array
    {
        $typeLines = [];
        $imports = [];

        foreach ($parsedFields as [$fieldName, $fieldType]) {
            $tsType = match ($fieldType) {
                'string', 'text' => 'string',
                'boolean' => 'boolean',
                'integer' => 'number',
                'datetime' => 'string',
                default => 'string',
            };

            if ($withMedia) {
                $typeLines[] = "  media: Media[];";
            }

            if (in_array($fieldType, ['fk', 'nfk'])) {
                $related = Str::studly(Str::replaceLast('_id', '', $fieldName));
                $propName = Str::replaceLast('_id', '', $fieldName);

                if ($fieldType === 'fk') {
                    $typeLines[] = "  {$fieldName}: {$related}['id'];";
                    $typeLines[] = "  {$propName}: {$related};";
                } else {
                    $typeLines[] = "  {$fieldName}?: {$related}['id'];";
                    $typeLines[] = "  {$propName}?: {$related};";
                }

                $imports[] = $related;
            } else {
                $typeLines[] = "  {$fieldName}: {$tsType};";
            }
        }

        return [$typeLines, array_values(array_unique($imports))];
    }

    protected function buildImportLines(array $imports, bool $withMedia): string
    {
        $importLines = '';
        if (!empty($imports)) {
            $importLines = collect($imports)
                ->map(fn($model) => 'import { ' . $model . ' } from "./' . Str::kebab($model) . '";')
                ->implode("\n");
        }
        if ($withMedia) {
            $importLines .= "\nimport { Media } from '.';\n";
        }
        return $importLines . "\n\n";
    }

    protected function updateTypeDefinition(string $name, string $importLines, array $typeLines): void
    {
        $dtsPath = "resources/js/types/{$name}.d.ts";
        if (File::exists($dtsPath)) {
            $content = File::get($dtsPath);
            $content = str_replace('{{ imports }}', $importLines, $content);
            $content = str_replace('{{ fields }}', implode("\n", $typeLines), $content);
            File::put($dtsPath, $content);
            $this->info("Updated type definition: {$dtsPath}");
        } else {
            $this->warn("Type file not found: {$dtsPath}");
        }
    }


}
