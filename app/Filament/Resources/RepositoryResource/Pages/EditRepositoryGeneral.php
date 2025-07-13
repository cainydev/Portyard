<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Filament\Resources\RepositoryResource;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditRepositoryGeneral extends EditRecord
{
    protected static string $resource = RepositoryResource::class;

    protected static ?string $navigationLabel = "General";
    protected static ?string $navigationIcon = 'heroicon-m-squares-2x2';

    public function getBreadcrumbs(): array
    {
        return [static::getResource()::getPluralModelLabel(), $this->getTitle(), static::getNavigationLabel()];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->path;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make([
                View::make('filament.repository-general-header')
                    ->viewData(['repository' => $this->record]),
                Textarea::make('description')
                    ->hint(fn(string|null $state) => $state ? null : 'This repository has no description.')
                    ->live()
            ])->columnSpan(1),
            Section::make([
                View::make('filament.repository-push-info-short')
                    ->viewData(['repository' => $this->record])
            ])->columnSpan(1),
            Section::make([
                RichEditor::make('overview')
                    ->hint('An overview describes what your image does and how to run it. It displays in the public view of your repository once you have pushed some content.')
            ])
        ]);
    }
}
