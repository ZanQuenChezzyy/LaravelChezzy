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

// Fungsi untuk membatalkan proses dengan pesan error
function abortProcess($message)
{
    echo "\n\033[31m$message\033[0m\n";
    exit(0); // Keluar dari script
}

$models = [];

do {
    // Input nama model
    echo "\n\033[1;36mMasukkan nama model yang ingin Anda buat: \033[0m";
    $modelName = trim(fgets(STDIN));

    // Jika ada input model
    if (!empty($modelName)) {
        if (!in_array($modelName, $models)) {
            $models[] = $modelName;
        } else {
            echo "\033[31mModel '$modelName' sudah ada.\033[0m\n";
        }
    }

    // Cek apakah ingin menambahkan model lagi
    if (!empty($modelName)) {
        echo "\033[33mApakah ada lagi? (Y/N): \033[0m";
        $response = strtolower(trim(fgets(STDIN)));
    } else {
        // Jika tidak ada model yang dimasukkan, keluar dari loop
        break;
    }
} while ($response === 'y');

// Pengecekan jika tidak ada model yang ditambahkan
if (empty($models)) {
    abortProcess("Tidak ada model yang ditambahkan. Keluar dari script...");
}

// Menampilkan header dan konfirmasi model
showHeader("Konfirmasi Model yang Akan Dibuat");
echo "Model yang akan dibuat:\n";
foreach ($models as $model) {
    echo "\033[36m- $model\033[0m\n";
}

echo "\n\033[33mApakah Anda ingin melanjutkan pembuatan model ini? (Y/N): \033[0m";
$response = strtolower(trim(fgets(STDIN)));

if ($response !== 'y') {
    abortProcess("Proses dibatalkan.");
}

foreach ($models as $model) {
    echo "\n\033[1;36mMembuat model: \033[0m$model\n";

    // Jalankan perintah artisan
    exec("php artisan make:model $model -m", $output, $returnCode);

    if ($returnCode === 0) {
        // Path ke file model yang baru saja dibuat
        $modelFilePath = __DIR__ . "/app/Models/$model.php";

        // Pastikan file model ada
        if (file_exists($modelFilePath)) {
            // Isi template untuk model
            $modelContent = "<?php\n\nnamespace App\Models;\n\nuse Illuminate\Database\Eloquent\Factories\HasFactory;\nuse Illuminate\Database\Eloquent\Model;\n\nclass $model extends Model\n{\n    use HasFactory;\n\n    protected \$fillable = [\n        'nama',\n    ];\n}";

            // Tulis ulang isi file model dengan template di atas
            file_put_contents($modelFilePath, $modelContent);
        }
    } else {
        echo "\033[31mGagal membuat model $model. Pesan error:\n\033[0m";
        echo implode("\n", $output) . "\n";
    }

    // Bersihkan output buffer untuk perintah berikutnya
    $output = [];

    // Delay 0,5 detik
    usleep(700000); // 500,000 mikrodetik = 0,5 detik
}

// Gabungkan nama model yang berhasil dibuat menjadi satu string yang dipisahkan koma
$modelsList = implode(', ', $models);

// Menampilkan nama model yang berhasil dibuat dengan warna cyan
echo "\n\033[32mModel ($modelsList) Berhasil Dibuat.\033[0m\n";
