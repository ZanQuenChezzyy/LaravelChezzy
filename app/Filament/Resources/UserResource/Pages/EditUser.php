<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;
use JoseEspinal\RecordNavigation\Traits\HasRecordNavigation;

class EditUser extends EditRecord
{
    use HasRecordNavigation;
    protected static string $resource = UserResource::class;
    protected function getHeaderActions(): array
    {
        $existingActions = [
            // Actions\DeleteAction::make()
            //     ->label('Hapus Pengguna')
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
