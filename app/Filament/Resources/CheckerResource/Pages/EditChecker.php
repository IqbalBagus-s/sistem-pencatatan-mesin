<?php

namespace App\Filament\Resources\CheckerResource\Pages;

use App\Filament\Resources\CheckerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChecker extends EditRecord
{
    protected static string $resource = CheckerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
