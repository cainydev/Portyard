<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Filament\Resources\RepositoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
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
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
