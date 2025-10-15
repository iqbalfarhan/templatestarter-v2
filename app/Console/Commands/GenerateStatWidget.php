<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateStatWidget extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:statwidget {feature}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat stat widget untuk spesifik fitur';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $feature = strtolower($this->argument('feature'));

        $basePath = config('template-starter.generated-react-files-path');
        if (!$basePath) {
            $this->error('Konfigurasi path React tidak ditemukan pada config/template-starter.php (generated-react-files-path).');
            return self::FAILURE;
        }

        $targetDir = rtrim($basePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $feature . DIRECTORY_SEPARATOR . 'widget';

        if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
            $this->error('Gagal membuat direktori: ' . $targetDir);
            return self::FAILURE;
        }

        $fileName = $feature . '-stat-widget.tsx';
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

        if (file_exists($filePath)) {
            $this->error('File sudah ada: ' . $filePath);
            return self::FAILURE;
        }

        $componentName = Str::studly($feature) . 'StatWidget';
        $fileContents = <<<TSX
import { Card, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { StatData } from '@/types';
import { FC } from 'react';

type Props = {
  data: StatData[];
};

const {$componentName}: FC<Props> = ({ data }) => {
  return (
    <div className="grid-responsive grid gap-6">
      {data.map((item) => (
        <Card>
          <CardHeader>
            <CardDescription>{item.label}</CardDescription>
            <CardTitle>{item.value}</CardTitle>
          </CardHeader>
        </Card>
      ))}
    </div>
  );
};

export default {$componentName};
TSX;

        if (file_put_contents($filePath, $fileContents) === false) {
            $this->error('Gagal menulis file: ' . $filePath);
            return self::FAILURE;
        }

        $this->info('Berhasil membuat widget: ' . $filePath);
        return self::SUCCESS;
    }
}
