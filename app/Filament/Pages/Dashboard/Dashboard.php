<?php

namespace App\Filament\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static string $routePath = '/';
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $activeNavigationIcon = 'heroicon-s-home';
    protected static ?int $navigationSort = -2;
}
