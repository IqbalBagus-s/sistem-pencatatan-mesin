<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CheckerResource\Pages;
use App\Models\Checker;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class CheckerResource extends Resource
{
    protected static ?string $model = Checker::class;

    protected static ?string $navigationIcon = 'heroicon-o-check';
    protected static ?string $navigationGroup = 'Manajemen User'; // Mengelompokkan menu

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            TextInput::make('username')
                ->label('Username')
                ->required()
                ->unique(ignoreRecord: true),


            TextInput::make('password')
                ->label('Password')
                ->password()
                ->nullable() // Password tidak wajib diisi saat update
                ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                ->afterStateHydrated(fn ($state, $record) => $record ? '' : $state) // Kosongkan input saat form dimuat
                ->helperText('Kosongkan jika tidak ingin mengubah password')
                ->dehydrated(fn ($state) => filled($state)), // Hanya update jika diisi


            Select::make('status')
                ->label('Status')
                ->options([
                    'aktif' => 'Aktif',
                    'tidak_aktif' => 'Tidak Aktif',
                ])
                ->default('aktif')
                ->required(),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('username')->searchable(),
            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->colors([
                    'success' => 'aktif',
                    'danger' => 'tidak_aktif',
                ]),
            TextColumn::make('created_at')->label('Dibuat Pada')->dateTime(),
        ])->filters([
            SelectFilter::make('status')
                ->label('Filter Status')
                ->options([
                    'aktif' => 'Aktif',
                    'tidak_aktif' => 'Tidak Aktif',
                ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCheckers::route('/'),
            'create' => Pages\CreateChecker::route('/create'),
            'edit' => Pages\EditChecker::route('/{record}/edit'),
        ];
    }
}
