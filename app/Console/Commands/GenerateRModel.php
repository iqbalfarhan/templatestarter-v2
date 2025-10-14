<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;
use function Laravel\Prompts\textarea;

class GenerateRModel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // protected $signature = 'generate:rmodel {name} {--s|softDelete} {--fields=} {--m|media}';
    protected $signature = 'generate:rmodel {name?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Laravel model + React view secara berurutan (amodel + rview)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $feature = $this->getFeatureName();
        $options = $this->getOptionsSelection();
        $fields = $this->getFieldsList();

        $name = Str::snake($feature);
        $Name = Str::studly($feature);

        $softDelete = in_array('soft delete', $options, true);
        $media = in_array('media', $options, true);
        $fieldsCsv = $this->fieldsToCsv($fields);

        $this->info("🚀 Running generate:amodel {$Name} ...");
        $this->call('generate:amodel', [
            'name' => $Name,
            '--softDelete' => $softDelete,
            '--fields' => $fieldsCsv,
            '--media' => $media,
        ]);

        $this->info("🎨 Running generate:rview {$name} ...");
        $this->call('generate:rview', [
            'name' => $name,
            '--softDelete' => $softDelete,
            '--fields' => $fieldsCsv,
            '--media' => $media,
        ]);

        $this->info("✅ Done! {$Name} model + React view generated successfully!");

        return self::SUCCESS;
    }

    protected function getFeatureName(): string
    {
        $arg = $this->argument('name');
        if (is_string($arg) && trim($arg) !== '') {
            return trim($arg);
        }

        return text(
            label: 'Nama Model atau type yang mau lo buat',
            placeholder: 'E.g. Customer',
            required: true,
        );
    }

    /**
     * @return array<int, string>
     */
    protected function getOptionsSelection(): array
    {
        return multiselect(
            label: 'Option apa aja nih yang mau di pakai?',
            options: ['soft delete', 'media', 'stat-widget'],
            hint: 'Pilihlah pilihan yang mau di pakai, soft delete akan nambahin fitur soft delete di model dan controller, media akan nambahin fitur upload media'
        );
    }

    /**
     * @return array<int, string>
     */
    protected function getFieldsList(): array
    {
        $input = textarea(
            label: 'Kolom apa aja yang mau di buat?',
            placeholder: 'E.g. name:string',
            required: true,
            default: 'name:string',
            hint: 'gunakan format name:type. Pisahin pakai enter'
        );

        $rows = array_map('trim', explode(PHP_EOL, $input));
        $rows = array_values(array_filter($rows, fn($r) => $r !== ''));
        return $rows;
    }

    /**
     * @param array<int, string> $fields
     */
    protected function fieldsToCsv(array $fields): string
    {
        return implode(',', $fields);
    }
}
