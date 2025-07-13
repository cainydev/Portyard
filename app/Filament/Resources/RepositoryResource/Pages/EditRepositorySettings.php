<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Filament\Resources\RepositoryResource;
use App\Models\Repository;
use Filament\Forms\Components\Actions as FormActions;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;

class EditRepositorySettings extends EditRecord
{
    protected static string $resource = RepositoryResource::class;
    protected static ?string $navigationIcon = 'heroicon-m-cog-6-tooth';
    protected static ?string $navigationLabel = 'Settings';

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
            Section::make('Visibility')
                ->description(fn(Repository $record) => "This repository is " . ($record->public ? 'public' : 'private') . '.')
                ->schema([
                    View::make('filament.text')
                        ->viewData(['text' => 'Public repositories are available to anyone. Private repositories are only available to you.']),
                    ToggleButtons::make('public')
                        ->label('Public Repository')
                        ->live()
                        ->boolean()
                        ->grouped()
                        ->afterStateUpdated(function (Repository $record, bool $state) {
                            $record->public = $state;
                            $record->save();
                        })
                ]),
            Section::make('Delete repository')->schema([
                View::make('filament.text')
                    ->viewData(['text' => 'Deleting a repository will destroy all images stored within it! This action is not reversible.']),
                FormActions::make([
                    FormActions\Action::make('delete')
                        ->icon('heroicon-m-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Repository $record) {
                            $record->delete();
                            $this->redirect(ListRepositories::getUrl(), navigate: true);
                        })
                ])
            ])
        ]);
    }

    protected function getFormActions(): array
    {
        return [];
    }
}
