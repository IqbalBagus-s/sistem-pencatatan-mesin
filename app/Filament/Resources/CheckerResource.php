<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CheckerResource\Pages;
use App\Models\Checker;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

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
                ->unique(Checker::class, 'username'),

            TextInput::make('password')
                ->label('Password')
                ->password()
                ->required()
                ->dehydrateStateUsing(fn ($state) => bcrypt($state)), // Hash password
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('username')->searchable(),
            TextColumn::make('created_at')->label('Dibuat Pada')->dateTime(),
        ])->filters([]);
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
