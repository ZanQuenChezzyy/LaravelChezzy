<?php

namespace App\Filament\Clusters;

use App\Models\Role;
use Filament\Clusters\Cluster;

class RolePermission extends Cluster
{
    protected static ?string $model = Role::class;
    protected static ?string $navigationGroup = 'Kelola Pengguna';
    protected static ?string $title = 'Akses & Perizinan';
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $activeNavigationIcon = 'heroicon-s-shield-check';
    protected static ?int $navigationSort = 19;
    protected static ?string $slug = 'akses-perizinan';
}
