<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\RolePermission;
use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;
    protected static ?string $cluster = RolePermission::class;
    protected static ?string $label = 'Peran Pengguna';
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $activeNavigationIcon = 'heroicon-s-identification';
    protected static ?int $navigationSort = 18;
    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() < 2 ? 'danger' : 'info';
    }
    protected static ?string $navigationBadgeTooltip = 'Total Peran Pengguna';
    protected static ?string $slug = 'peran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    TextInput::make('name')
                        ->label('Nama Peran')
                        ->placeholder('Masukkan Nama Peran')
                        ->minLength(3)
                        ->maxLength(45)
                        ->unique(ignoreRecord: true)
                        ->required(),

                    Select::make('permissions')
                        ->label('Perizinan')
                        ->relationship('permissions', 'name')
                        ->placeholder('Pilih Perizinan')
                        ->multiple()
                        ->searchable()
                        ->native(false)
                        ->preload()
                        ->options(function () {
                            return \App\Models\Permission::orderBy('created_at', 'asc')  // Mengurutkan berdasarkan tanggal dibuat (dari yang lama ke baru)
                                ->pluck('name', 'id');
                        })
                        ->required(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nama Peran')
                    ->searchable(),

                TextColumn::make('permissions.name')
                    ->label('Perizinan')
                    ->colors([
                        'info',
                    ])
                    ->badge()
                    ->separator(', ')
                    ->limitList(4)
                    ->wrap(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    EditAction::make()
                        ->color('info'),
                    DeleteAction::make()
                        ->authorize(function ($record) {
                            return Auth::id() !== $record->id;
                        })
                        ->requiresConfirmation(),
                ])
                    ->icon('heroicon-o-ellipsis-horizontal-circle')
                    ->color('info')
                    ->tooltip('Aksi')
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->using(function ($records) {
                            $recordsToDelete = $records->reject(function ($record) {
                                return $record->id === Auth::id();
                            });
                            $recordsToDelete->each(function ($record) {
                                $record->delete();
                            });
                            session()->flash('message', 'Selected accounts were deleted, except for your own account.');
                        })
                        ->requiresConfirmation(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}