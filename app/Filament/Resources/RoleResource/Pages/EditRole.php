<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use JoseEspinal\RecordNavigation\Traits\HasRecordNavigation;

class EditRole extends EditRecord
{
    use HasRecordNavigation;
    protected static string $resource = RoleResource::class;
    protected function getHeaderActions(): array
    {
        $existingActions = [
            // Actions\DeleteAction::make()
            //     ->label('Hapus Level')
            //     ->authorize(function ($record) {
            //         return Auth::id() !== $record->id;
            //     })
            //     ->using(function ($record) {
            //         if ($record->id === Auth::id()) {
            //             session()->flash('error', 'You cannot delete your own account.');
            //             return false;
            //         }
            //         $record->delete();
            //     })
            //     ->requiresConfirmation(),
        ];
        return array_merge($existingActions, $this->getNavigationActions());
    }
}
