<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use JoseEspinal\RecordNavigation\Traits\HasRecordsList;

class ListRoles extends ListRecords
{
    use HasRecordsList;
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Peran')
                ->icon('heroicon-m-plus-circle'),
        ];
    }
}
