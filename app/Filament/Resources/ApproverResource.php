<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApproverResource\Pages;
use App\Models\Approver;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;

class ApproverResource extends Resource
{
    protected static ?string $model = Approver::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $navigationGroup = 'Manajemen User'; // Mengelompokkan menu

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            TextInput::make('username')
                ->label('Username')
                ->required()
                ->unique(Approver::class, 'username'),

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
            'index' => Pages\ListApprovers::route('/'),
            'create' => Pages\CreateApprover::route('/create'),
            'edit' => Pages\EditApprover::route('/{record}/edit'),
        ];
    }
}
