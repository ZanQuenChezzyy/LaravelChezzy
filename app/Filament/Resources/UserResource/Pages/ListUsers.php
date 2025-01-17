<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\Pages\ListRecords;
use JoseEspinal\RecordNavigation\Traits\HasRecordsList;

class ListUsers extends ListRecords
{
    use HasRecordsList;
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Pengguna')
                ->icon('heroicon-m-user-plus'),
            ExportAction::make()
                ->exporter(UserExporter::class)
                ->label('Ekspor Pengguna')
                ->icon('heroicon-m-document-arrow-up')
                ->formats([
                    ExportFormat::Xlsx,
                    ExportFormat::Csv,
                ])
        ];
    }
}
