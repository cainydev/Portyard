<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RepositoryResource\Pages;
use App\Filament\Resources\RepositoryResource\RelationManagers;
use App\Models\Repository;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RepositoryResource extends Resource
{
    protected static ?string $model = Repository::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('users', function ($query) {
                $query->where('users.id', auth()->id());
            });
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->prefix(fn() => auth()->user()->namespace . '/')
                    ->label('Name')
                    ->searchable()
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\UsersRelationManager::class,
            RelationManagers\TagsRelationManager::class,
        ];
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRepositories::route('/'),
            'create' => Pages\CreateRepository::route('/create'),
            'edit' => Pages\EditRepository::route('/{record}/edit'),
        ];
    }
}
