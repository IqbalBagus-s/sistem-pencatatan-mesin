<?php

namespace App\Filament\Resources\ApproverResource\Pages;

use App\Filament\Resources\ApproverResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApprovers extends ListRecords
{
    protected static string $resource = ApproverResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
