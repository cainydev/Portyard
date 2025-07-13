<?php

namespace App\Filament\Resources\RepositoryResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class WebhooksRelationManager extends RelationManager
{
    protected static string $relationship = 'webhooks';

    public function table(Table $table): Table
    {
        return $table
            ->header(fn(Table $table) => view('filament.repository-collaborators-header', [
                'actions' => $table->getHeaderActions(),
                'repository' => $this->ownerRecord
            ]))
            ->recordTitleAttribute('name')
            ->paginated(false)
            ->columns([
                TextInput::make('name')
                    ->required()
                    ->columnSpan(1),
                TextInput::make('url')
                    ->label('Callback URL')
                    ->required()
                    ->url()
                    ->columnSpan(1)
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
