<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Flex;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\View;
use App\Filament\Resources\RepositoryResource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;

class CreateRepository extends CreateRecord
{
    protected static string $resource = RepositoryResource::class;

    protected static bool $canCreateAnother = false;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Flex::make([
                    Section::make([
                        Hidden::make('namespace')
                            ->dehydrateStateUsing(fn() => auth()->user()->namespace),
                        TextInput::make('name')
                            ->prefix(fn(Get $get) => auth()->user()->namespace . ' /')
                            ->live()
                            ->alphaDash()
                            ->required(),
                        Textarea::make('description')
                            ->placeholder('Short description'),
                        Radio::make('public')
                            ->label('Visibility')
                            ->default(false)
                            ->options([
                                true => 'Public',
                                false => 'Private',
                            ])
                            ->descriptions([
                                true => 'Appears in ' . config('app.name') . ' search results.',
                                false => 'Only visible to you.',
                            ])
                            ->inline()
                            ->required()
                            ->inlineLabel(false)
                    ])->grow(),
                    Section::make([
                        View::make('info')
                            ->live()
                            ->view('filament.repository-push-info')
                            ->formatStateUsing(fn(Get $get) => ['name' => ($get('name'))])
                    ])->grow(false)
                ])->columnSpanFull()
            ]);
    }
}
