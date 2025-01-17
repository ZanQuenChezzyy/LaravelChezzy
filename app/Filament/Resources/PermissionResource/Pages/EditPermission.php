<?php

namespace App\Filament\Resources\PermissionResource\Pages;

use App\Filament\Resources\PermissionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use JoseEspinal\RecordNavigation\Traits\HasRecordNavigation;

class EditPermission extends EditRecord
{
    use HasRecordNavigation;
    protected static string $resource = PermissionResource::class;
    protected function getHeaderActions(): array
    {
        $existingActions = [
            Actions\DeleteAction::make()
                ->label('Hapus Perizinan')
                ->icon('heroicon-m-trash'),
        ];
        return array_merge($existingActions, $this->getNavigationActions());
    }
}
