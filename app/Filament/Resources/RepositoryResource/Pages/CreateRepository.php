<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Filament\Resources\RepositoryResource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Pages\CreateRecord;

class CreateRepository extends CreateRecord
{
    protected static string $resource = RepositoryResource::class;

    protected static bool $canCreateAnother = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Split::make([
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
