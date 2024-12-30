<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Auth\EditProfile as BaseEditProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditProfile extends BaseEditProfile
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('EditProfile')
                    ->tabs([
                        Tab::make('Informasi Pribadi')
                            ->schema([
                                FileUpload::make('avatar_url')
                                    ->label('Foto Profil')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatios([
                                        '1:1',
                                    ])
                                    ->imageCropAspectRatio('1:1')
                                    ->directory('avatar_upload')
                                    ->visibility('public')
                                    ->helperText('Format yang didukung: JPG, PNG, atau GIF.')
                                    ->columnSpanFull(),

                                TextInput::make('name')
                                    ->label(__('filament-panels::pages/auth/edit-profile.form.name.label'))
                                    ->placeholder(__('Masukkan Nama'))
                                    ->inlineLabel()
                                    ->required()
                                    ->maxLength(255)
                                    ->autofocus(),

                                TextInput::make('email')
                                    ->label(__('filament-panels::pages/auth/edit-profile.form.email.label'))
                                    ->placeholder(__('Masukkan Email'))
                                    ->inlineLabel()
                                    ->email()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true),
                            ]),

                        Tab::make('Kata Sandi')
                            ->schema([
                                TextInput::make('password')
                                    ->label(__('Kata Sandi'))
                                    ->placeholder(__('Kosongkan jika tidak ingin mengubah'))
                                    ->password()
                                    ->revealable(filament()->arePasswordsRevealable())
                                    ->rule(Password::default())
                                    ->autocomplete('new-password')
                                    ->dehydrated(fn($state): bool => filled($state))
                                    ->dehydrateStateUsing(fn($state): string => Hash::make($state))
                                    ->live(debounce: 500)
                                    ->same('passwordConfirmation'),

                                TextInput::make('passwordConfirmation')
                                    ->label(__('Konfirmasi Kata Sandi'))
                                    ->placeholder(__('Masukkan lagi Kata sandi'))
                                    ->password()
                                    ->revealable(filament()->arePasswordsRevealable())
                                    ->required()
                                    ->visible(fn(Get $get): bool => filled($get('password')))
                                    ->dehydrated(false),
                            ])
                    ]),
            ]);
    }
}
