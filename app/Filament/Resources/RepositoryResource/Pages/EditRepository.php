<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Filament\Resources\RepositoryResource;
use App\Models\Repository;
use Filament\Forms\Components\Actions as FormActions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;

class EditRepository extends EditRecord
{
    protected static string $resource = RepositoryResource::class;

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Tabs::make('tabs')->tabs([
                static::generalTab(),
                static::tagsTab(),
                static::webhooksTab(),
                static::settingsTab()
            ])
                ->columnSpanFull()
        ]);
    }

    public function generalTab(): Tab
    {
        return Tab::make('General')->schema([
            Section::make([
                View::make('filament.repository-general-header')
                    ->viewData(['repo' => $this->getRecord()]),
                Textarea::make('description')
                    ->hint(fn(string|null $state) => $state ? null : 'This repository has no description.')
                    ->live()
            ])->columnSpan(1),
            Section::make([
                View::make('filament.repository-push-info-short')
                    ->viewData(['repo' => $this->getRecord()])
            ])->columnSpan(1),
            Section::make([
                RichEditor::make('overview')
                    ->hint('An overview describes what your image does and how to run it. It displays in the public view of your repository once you have pushed some content.')
            ])
        ])->columns(2);
    }

    public function tagsTab(): Tab
    {
        return Tab::make('Tags')->schema([
            Split::make([
                Select::make('sort_by')
                    ->options([
                        'newest' => 'Newest',
                        'oldest' => 'Oldest',
                        'alphabetical' => 'A - Z',
                        'reverse_alphabetical' => 'Z - A',
                    ])
                    ->default('newest')
                    ->inlineLabel()
                    ->grow(false),
                TextInput::make('search')
                    ->placeholder('Search tags')
                    ->hiddenLabel()
                    ->grow(false),
            ]),
            Repeater::make('tags')
                ->relationship()
                ->itemLabel(fn(array $state): ?string => $state['name'])
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->columnSpan(1),
                    TextInput::make('description')
                        ->columnSpan(1),
                ])->columns()
        ]);
    }

    public function webhooksTab(): Tab
    {
        return Tab::make('Webhooks')->schema([
            View::make('filament.text')->viewData(['text' => 'A webhook is an HTTP call-back triggered by a specific event. You can create a single webhook to start and connect multiple webhooks to further build out your workflow.']),
            View::make('filament.text')->viewData(['text' => 'When an image is pushed to this repo, your workflows will kick off based on your specified webhooks.']),
            Section::make('Active Webhooks')->schema([
                Repeater::make('webhooks')
                    ->hiddenLabel()
                    ->relationship()
                    ->itemLabel(fn(array $state): ?string => $state['name'] . ': ' . $state['url'] ?? null)
                    ->addActionLabel('Create new Webhook')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->columnSpan(1),
                        TextInput::make('url')
                            ->label('Callback URL')
                            ->required()
                            ->url()
                            ->columnSpan(1)
                    ])->columns()
            ])
        ]);
    }

    public function settingsTab(): Tab
    {
        return Tab::make('Settings')->schema([
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
                    Action::make('delete')
                        ->icon('heroicon-m-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Repository $record) {
                            $record->delete();
                            $this->redirect(route('filament.registry.resources.repositories.index'), navigate: true);
                        })
                ])
            ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
