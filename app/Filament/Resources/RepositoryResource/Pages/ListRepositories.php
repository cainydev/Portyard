<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Filament\Resources\RepositoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ListRepositories extends ListRecords
{
    protected static string $resource = RepositoryResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('path')
                    ->weight(FontWeight::Bold)
                    ->label('Repository')
                    ->searchable()
                    ->sortable()
                    ->width(300),
                TextColumn::make('public')
                    ->label('Visibility')
                    ->icon(fn($state) => $state ? 'heroicon-o-globe-alt' : 'heroicon-o-lock-closed')
                    ->iconColor(fn($state) => $state ? 'success' : 'info')
                    ->formatStateUsing(fn($state) => $state ? 'Public' : 'Private')
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
