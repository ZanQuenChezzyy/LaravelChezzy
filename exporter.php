#!/usr/bin/env php
<?php

// Fungsi untuk menampilkan header
function showHeader($title)
{
    echo "\n\033[36m";
    echo str_repeat('=', 50) . "\n";
    echo str_pad($title, 50, ' ', STR_PAD_BOTH) . "\n";
    echo str_repeat('=', 50) . "\033[0m\n";
}

// Fungsi untuk menampilkan pesan pembatalan
function abortProcess()
{
    echo "\n\033[31mProses dibatalkan oleh pengguna.\033[0m\n";
    exit(0);
}

// Path ke folder models
$modelsPath = __DIR__ . '/app/Models';

// Periksa apakah folder models ada
if (!is_dir($modelsPath)) {
    echo "\033[31mFolder 'app/Models' tidak ditemukan.\033[0m\n";
    exit(1);
}

// Ambil semua file PHP dari folder models
$modelFiles = glob($modelsPath . '/*.php');

// Pastikan ada file model
if (empty($modelFiles)) {
    echo "\033[33mTidak ada file model di folder 'app/Models'.\033[0m\n";
    exit(0);
}

// Ambil nama model dari nama file
$models = array_map(function ($filePath) {
    return pathinfo($filePath, PATHINFO_FILENAME);
}, $modelFiles);

// Daftar model yang ditemukan
showHeader('Model yang Ditemukan');
foreach ($models as $index => $model) {
    echo sprintf("\033[36m[%d] %s\033[0m\n", $index + 1, $model);
}

// Array untuk menyimpan model yang akan dibuatkan exporter
$selectedModels = [];

// Pilih model untuk dibuatkan exporter
do {
    echo "\n\033[33mMasukkan nomor model yang ingin dibuat exporter : \033[0m";
    $input = trim(fgets(STDIN));

    // Periksa apakah input valid
    if (!is_numeric($input) || !isset($models[$input - 1])) {
        echo "\033[31mInput tidak valid. Silakan masukkan nomor yang benar.\033[0m\n";
        continue;
    }

    $selectedModels[] = $models[$input - 1];
    $selectedModels = array_unique($selectedModels);

    echo "\033[33mApakah ada lagi? (Y/N) : \033[0m";
    $response = strtolower(trim(fgets(STDIN)));

    if ($response === null || feof(STDIN)) {
        abortProcess();
    }
} while ($response === 'y');

if (empty($selectedModels)) {
    echo "\033[33mTidak ada model yang dipilih untuk dibuatkan exporter.\033[0m\n";
    exit(0);
}

// Konfirmasi sebelum proses pembuatan exporter
echo "\n\033[33mLanjutkan proses pembuatan exporter? (Y/N)\033[0m ";
$response = strtolower(trim(fgets(STDIN)));

if ($response !== 'y') {
    abortProcess();
}

// Proses pembuatan exporter dan penambahan logika ExportAction
foreach ($selectedModels as $model) {
    $command = "php artisan make:filament-exporter $model --generate";

    echo "\n\033[36mMembuat Exporter: \033[0m$model\n";

    $output = shell_exec($command);

    // Cek keberhasilan pembuatan exporter
    if (strpos($output, 'successfully') !== false) {
        echo "\033[32mExporter $model berhasil dibuat.\033[0m\n";

        // Path resource terkait
        $resourcePagesPath = __DIR__ . "/app/Filament/Resources/{$model}Resource/Pages";
        $listFile = "{$resourcePagesPath}/List{$model}s.php";
        $manageFile = "{$resourcePagesPath}/Manage{$model}s.php";
        $resourceFile = __DIR__ . "/app/Filament/Resources/{$model}Resource.php";

        // Tentukan file yang akan diedit
        $targetFile = null;
        if (file_exists($listFile)) {
            $targetFile = $listFile;
        } elseif (file_exists($manageFile)) {
            $targetFile = $manageFile;
        }

        if ($targetFile) {
            // Tambahkan ExportAction ke file yang ditemukan
            $fileContent = file_get_contents($targetFile);

            $exportActionCode = <<<EOD
            ExportAction::make()
                ->exporter({$model}Exporter::class)
                ->label('Ekspor {$model}')
                ->formats([
                    ExportFormat::Xlsx,
                    ExportFormat::Csv,
                ])
EOD;

            // Cari fungsi getHeaderActions
            $useStatements = <<<PHP
use App\Filament\Exports\\{$model}Exporter;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
PHP;

            // Periksa dan tambahkan blok import jika belum ada
            if (!str_contains($fileContent, "use App\Filament\Exports\\" . $model . "Exporter;")) {
                // Menyisipkan import setelah namespace
                if (preg_match('/^namespace\s+[^;]+;/m', $fileContent, $matches)) {
                    $fileContent = str_replace($matches[0], $matches[0] . "\n\n" . $useStatements, $fileContent);
                    echo "\033[32mImport yang diperlukan ditambahkan ke {$targetFile}.\033[0m\n";
                } else {
                    echo "\033[31mNamespace tidak ditemukan di {$targetFile}. Pastikan file ini valid.\033[0m\n";
                }
            }

            // Blok kode ExportAction yang ingin ditambahkan
            $exportActionCode = <<<PHP
            ExportAction::make()
                ->exporter({$model}Exporter::class)
                ->label('Ekspor {$model}')
                ->formats([
                    ExportFormat::Xlsx,
                    ExportFormat::Csv,
                ]),
PHP;

            // Cari fungsi getHeaderActions dan tambahkan ExportAction
            if (preg_match('/protected\s+function\s+getHeaderActions\s*\(\s*\)\s*:\s*array\s*\{\s*return\s*\[\s*(.*?)\s*\];\s*\}/s', $fileContent, $matches)) {
                $actionsBlock = $matches[1];
                if (!str_contains($actionsBlock, "ExportAction::make()")) {
                    // Tambahkan ExportAction ke dalam array
                    $updatedActionsBlock = $actionsBlock . "\n" . $exportActionCode;
                    $fileContent = str_replace($matches[0], str_replace($actionsBlock, $updatedActionsBlock, $matches[0]), $fileContent);
                    echo "\033[32mExportAction ditambahkan ke fungsi getHeaderActions() di {$targetFile}.\033[0m\n";
                } else {
                    echo "\033[33mExportAction sudah ada di fungsi getHeaderActions(). Tidak ada perubahan.\033[0m\n";
                }
            } else {
                echo "\033[31mFungsi getHeaderActions() tidak ditemukan atau tidak sesuai format di {$targetFile}.\033[0m\n";
            }

            // Tulis kembali file dengan perubahan
            file_put_contents($targetFile, $fileContent);
        } else {
            echo "\033[31mFile resource untuk {$model} tidak ditemukan.\033[0m\n";
        }

        // Cari blok kode ->bulkActions([...])
        // Cari blok kode ->bulkActions([...])
        if (file_exists($resourceFile)) {
            $fileContent = file_get_contents($resourceFile);

            // Tambahkan baris "use" jika belum ada
            $usesToAdd = [
                'use Filament\Tables\Actions\ExportBulkAction;',
                'use App\Filament\Exports\\' . $model . 'Exporter;',
                'use Filament\Actions\Exports\Enums\ExportFormat;',
            ];

            foreach ($usesToAdd as $useStatement) {
                if (!str_contains($fileContent, $useStatement)) {
                    if (preg_match('/namespace\s+.*?;/', $fileContent, $namespaceMatch)) {
                        $fileContent = str_replace(
                            $namespaceMatch[0],
                            $namespaceMatch[0] . "\n" . $useStatement,
                            $fileContent
                        );
                    }
                }
            }

            if (preg_match('/->bulkActions\(\[\s*(.*?)\s*\]\)/s', $fileContent, $matches)) {
                $actionsBlock = $matches[1];

                // Blok kode ExportBulkAction yang ingin ditambahkan
                $bulkActionCode = <<<PHP
            ExportBulkAction::make()
                ->exporter({$model}Exporter::class)
                ->label('Ekspor {$model}')
                ->formats([
                    ExportFormat::Xlsx,
                    ExportFormat::Csv,
                ]),
        PHP;

                if (!str_contains($actionsBlock, "ExportBulkAction::make()")) {
                    // Tambahkan ExportBulkAction ke dalam array bulkActions
                    $updatedActionsBlock = $actionsBlock . "\n" . $bulkActionCode;
                    $fileContent = str_replace($matches[0], str_replace($actionsBlock, $updatedActionsBlock, $matches[0]), $fileContent);
                    echo "\033[32mExportBulkAction ditambahkan ke ->bulkActions() di {$resourceFile}.\033[0m\n";
                } else {
                    echo "\033[33mExportBulkAction sudah ada di ->bulkActions(). Tidak ada perubahan.\033[0m\n";
                }
            } else {
                echo "\033[31m->bulkActions() tidak ditemukan atau tidak sesuai format di {$resourceFile}.\033[0m\n";
            }

            // Tulis ulang file jika ada perubahan
            file_put_contents($resourceFile, $fileContent);
        } else {
            echo "\033[31mFile resource untuk {$model} tidak ditemukan di {$resourceFile}.\033[0m\n";
        }
    } else {
        echo "\033[31mGagal membuat exporter untuk $model.\033[0m\n";
        echo $output . "\n";
    }
}

echo "\n\033[32mSemua exporter berhasil dibuat. Silakan modifikasi exporter yang telah dibuat.\033[0m\n";
