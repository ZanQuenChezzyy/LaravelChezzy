#!/usr/bin/env php
<?php

// Helper untuk mengubah string menjadi camel case
function camelCase($string)
{
    $string = str_replace(['-', '_'], ' ', $string);
    $string = ucwords($string);
    $string = lcfirst(str_replace(' ', '', $string));
    return $string;
}

// Helper untuk mengubah string menjadi plural (sederhana)
function plural($string)
{
    if (substr($string, -1) === 'y') {
        return substr($string, 0, -1) . 'ies';
    }
    return $string . 's';
}

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
    exit(0);
}

// Fungsi untuk membuat fungsi relasi Eloquent
function createRelationMethod($relationType, $relatedModel)
{
    $relationMap = [
        '1' => 'HasMany',
        '2' => 'HasOne',
    ];

    $relation = $relationMap[$relationType] ?? null;
    if (!$relation) {
        abortProcess("Tipe relasi tidak valid.");
    }

    $methodName = camelCase(plural($relatedModel));
    $relatedClass = "\\App\\Models\\$relatedModel";

    // Konversi nama model ke snake_case
    $snakeCaseModel = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $relatedModel));

    switch ($relation) {
        case 'HasMany':
            return "public function $methodName(): \\Illuminate\\Database\\Eloquent\\Relations\\HasMany\n{\n    return \$this->hasMany($relatedClass::class);\n}\n";
        case 'HasOne':
            return "public function $methodName(): \\Illuminate\\Database\\Eloquent\\Relations\\HasOne\n{\n    return \$this->hasOne($relatedClass::class);\n}\n";
    }

    return '';
}

// Fungsi untuk membuat relasi BelongsTo
function createBelongsToMethod($relatedModel)
{
    $methodName = $relatedModel; // Menggunakan nama model tanpa modifikasi
    $relatedClass = "\\App\\Models\\$relatedModel";
    $foreignKey = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $relatedModel)) . "_id";
    $ownerKey = "id";

    return "public function $methodName(): \\Illuminate\\Database\\Eloquent\\Relations\\BelongsTo\n{\n" .
        "    return \$this->belongsTo($relatedClass::class, '$foreignKey', '$ownerKey');\n}\n";
}

// Daftar model yang ada
$modelsDir = __DIR__ . '/app/Models';
$models = array_map(function ($file) {
    return basename($file, '.php');
}, glob("$modelsDir/*.php"));

if (empty($models)) {
    abortProcess("Tidak ada model yang ditemukan di direktori $modelsDir.");
}

// Menampilkan model yang ditemukan
showHeader("Model yang Ditemukan");
foreach ($models as $index => $model) {
    echo ($index + 1) . ". $model\n";
}

// Memilih model sumber
echo "\n\033[1;36mMasukkan nomor model sumber: \033[0m";
$sourceIndex = (int) trim(fgets(STDIN)) - 1;

if (!isset($models[$sourceIndex])) {
    abortProcess("Model sumber tidak valid.");
}

$sourceModel = $models[$sourceIndex];
$relations = [];

// Memilih jenis relasi sekali saja
showHeader("Pilih Tipe Relasi");
echo "1. Has Many\n";
echo "2. Has One\n";
echo "\033[1;36mMasukkan nomor tipe relasi: \033[0m";
$relationType = trim(fgets(STDIN));

if (!in_array($relationType, ['1', '2'])) {
    abortProcess("Tipe relasi tidak valid.");
}

// Loop untuk menambahkan relasi
do {
    // Menampilkan model tujuan
    showHeader("Model yang Ditemukan");
    foreach ($models as $index => $model) {
        if ($model !== $sourceModel) {
            echo ($index + 1) . ". $model\n";
        }
    }

    // Memilih model tujuan
    echo "\n\033[1;36mMasukkan nomor model tujuan: \033[0m";
    $targetIndex = (int) trim(fgets(STDIN)) - 1;

    if (!isset($models[$targetIndex]) || $models[$targetIndex] === $sourceModel) {
        abortProcess("Model tujuan tidak valid.");
    }

    $targetModel = $models[$targetIndex];
    $relations[] = [
        'source' => $sourceModel,
        'target' => $targetModel,
        'type' => $relationType,
    ];

    echo "\033[33mApakah ada lagi? (Y/N): \033[0m";
    $response = strtolower(trim(fgets(STDIN)));
} while ($response === 'y');

// Membuat relasi pada model sumber dan tujuan
foreach ($relations as $relation) {
    $sourceFile = "$modelsDir/{$relation['source']}.php";
    $targetFile = "$modelsDir/{$relation['target']}.php";
    $relationMethodSource = createRelationMethod($relation['type'], $relation['target']);
    $relationMethodTarget = '';

    // Jika HasMany atau HasOne, tambahkan relasi BelongsTo di model tujuan
    if (in_array($relation['type'], ['1', '2'])) { // 1 = HasMany, 2 = HasOne
        $relationMethodTarget = createBelongsToMethod($relation['source']);
    }

    // Tambahkan relasi ke model sumber
    if (file_exists($sourceFile)) {
        $fileContent = file_get_contents($sourceFile);

        if (!str_contains($fileContent, "function " . camelCase(plural($relation['target'])) . "()")) {
            $fileContent = preg_replace('/}\s*$/', "\n    $relationMethodSource\n}", $fileContent);
            file_put_contents($sourceFile, $fileContent);
            echo "\033[32mRelasi dari {$relation['source']} ke {$relation['target']} berhasil ditambahkan.\033[0m\n";
        } else {
            echo "\033[33mRelasi sudah ada di model {$relation['source']}.\033[0m\n";
        }
    } else {
        echo "\033[31mFile model {$relation['source']} tidak ditemukan.\033[0m\n";
    }

    // Tambahkan relasi BelongsTo ke model tujuan jika ada
    if ($relationMethodTarget && file_exists($targetFile)) {
        $fileContent = file_get_contents($targetFile);

        if (!str_contains($fileContent, "function " . camelCase($relation['source']) . "()")) {
            $fileContent = preg_replace('/}\s*$/', "\n    $relationMethodTarget\n}", $fileContent);
            file_put_contents($targetFile, $fileContent);
            echo "\033[32mRelasi BelongsTo dari {$relation['target']} ke {$relation['source']} berhasil ditambahkan.\033[0m\n";
        } else {
            echo "\033[33mRelasi sudah ada di model {$relation['target']}.\033[0m\n";
        }
    }
}
