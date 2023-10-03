<?php

namespace App\Filament\Resources\DocumentResource\Pages;

use App\Filament\Resources\DocumentResource;
use App\Models\Document;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ListDocuments extends ListRecords
{
    protected static string $resource = DocumentResource::class;

    protected function getTableQuery(): ?Builder
    {
        return static::getResource()::getEloquentQuery()
            ->where(function ($query) {
                $query->where(function ($subquery) {
                    $subquery->where('is_public', 1)
                        ->orWhere(function ($uploaderQuery) {
                            $uploaderQuery->where('is_public', 0)
                                ->where('created_by', Auth::id());
                        });
                })
                    ->orWhereHas('tracks', function ($subquery) {
                        $subquery->where('user_id', Auth::id())
                            ->where('is_shared', 1);
                    });
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
