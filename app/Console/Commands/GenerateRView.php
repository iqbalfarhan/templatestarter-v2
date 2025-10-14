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

        // Update table content in index.tsx
        $this->updateTableContent($basePath, $parsedFields, $name);
        $this->updateFormField($basePath, $parsedFields, $name);

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
                        {
                            title: 'Archived',
                            icon: Archive,
                            onClick: () => router.visit(route('{$name}.archived')),
                        },
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

    protected function generateTableHeading(array $parsedFields): string
    {
        $template = "";
        
        foreach ($parsedFields as [$fieldName, $fieldType]) {
            $displayName = Str::title(str_replace('_', ' ', $fieldName));
            $template .= "                <TableHead>{$displayName}</TableHead>\n";
        }

        return $template;
    }

    protected function generateTableCell(array $parsedFields, string $name): string
    {
        $template = "";
        
        foreach ($parsedFields as [$fieldName, $fieldType]) {
            if (in_array($fieldType, ['fk', 'nfk'])) {
                $propName = Str::replaceLast('_id', '', $fieldName);
                $template .= "                <TableCell>{{$name}.{$propName}?.name || '-'}</TableCell>\n";
            } else {
                $template .= "                <TableCell>{{$name}.{$fieldName}}</TableCell>\n";
            }
        }

        return $template;
    }

    protected function updateTableContent(string $basePath, array $parsedFields, string $name): void
    {
        // Generate table headings and cells
        $tableHeads = $this->generateTableHeading($parsedFields);
        $tableCells = $this->generateTableCell($parsedFields, $name);

        // Update index.tsx with table replacements
        $indexFilePath = $basePath . '/index.tsx';
        if (File::exists($indexFilePath)) {
            $content = File::get($indexFilePath);
            $content = str_replace('{{ TableHeads }}', $tableHeads, $content);
            $content = str_replace('{{ TableCells }}', $tableCells, $content);
            File::put($indexFilePath, $content);
            $this->info("Updated table content in: {$indexFilePath}");
        } else {
            $this->warn("Index file not found: {$indexFilePath}");
        }
    }

    protected function generateUseFormObject(array $parsedFields, string $name):string
    {
        $template = "";
        foreach ($parsedFields as [$fieldName, $fieldType]) {
            $template .= "{$fieldName} : {$name}?.{$fieldName} ?? '',\n";
        }

        return $template;
    }

    protected function generateFormField(array $parsedFields, $name): string
    {
        $template = [];
        foreach ($parsedFields as [$fieldName, $fieldType]) {
            $displayName = Str::title(str_replace('_', ' ', $fieldName));
            
            if ($fieldType === 'text') {
                $template[] = <<<PHP
                    <FormControl label="{$displayName}">
                        <Textarea placeholder="Enter {$displayName}" value={data.{$fieldName}} onChange={(e) => setData('{$fieldName}', e.target.value)} />
                    </FormControl>
                PHP;
            } 
            elseif(in_array($fieldType, ['fk', 'nfk'])){
                $template[] = <<<PHP
                    <FormControl label="{$displayName}">
                        <Select value={data.{$fieldName}.toString()} onValueChange={(value) => setData('{$fieldName}', value)}>
                            <SelectTrigger>
                                <SelectValue placeholder="Pilih {$displayName}" />
                            </SelectTrigger>
                            <SelectContent>
                                {/* {items.map((item) => (
                                    <SelectItem key={item.id} value={item.id.toString()}>{item.name}</SelectItem>
                                ))}  */}
                                <SelectItem value={data.{$fieldName}.toString()}>{data.{$fieldName}}</SelectItem>
                            </SelectContent>
                        </Select>
                    </FormControl>
                PHP;
            }
            else {
                $inputType = match($fieldType) {
                    'boolean' => 'checkbox',
                    'integer' => 'number',
                    'datetime' => 'datetime-local',
                    default => 'text'
                };
                
                $template[] = <<<PHP
                    <FormControl label="{$displayName}">
                        <Input type="{$inputType}" placeholder="Enter {$displayName}" value={data.{$fieldName}} onChange={(e) => setData('{$fieldName}', e.target.value)} />
                    </FormControl>
                PHP;
            }
        }

        return implode("\n", $template);
    }

    protected function updateFormField(string $basePath, array $parsedFields, string $name): void
    {
        $useFormObject = $this->generateUseFormObject($parsedFields, $name);
        $formFields = $this->generateFormField($parsedFields, $name);

        // Update index.tsx with table replacements
        $indexFilePath = $basePath . "/components/$name-form-sheet.tsx";
        if (File::exists($indexFilePath)) {
            $content = File::get($indexFilePath);
            $content = str_replace('{{ useFormObject }}', $useFormObject, $content);
            $content = str_replace('{{ formFields }}', $formFields, $content);
            File::put($indexFilePath, $content);
            $this->info("Updated table content in: {$indexFilePath}");
        } else {
            $this->warn("Index file not found: {$indexFilePath}");
        }
    }


}
