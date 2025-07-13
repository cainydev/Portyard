<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Enums\WebhookTrigger;
use App\Filament\Resources\RepositoryResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ManageRepositoryWebhooks extends ManageRelatedRecords
{
    protected static string $resource = RepositoryResource::class;
    protected static ?string $navigationLabel = 'Webhooks';
    protected static ?string $navigationIcon = 'heroicon-s-cloud-arrow-up';
    protected static string $relationship = 'webhooks';

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
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->hint('just for you')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('url')
                    ->url()
                    ->hint('include http(s)')
                    ->required(),
                Forms\Components\Select::make('trigger')
                    ->options(collect(WebhookTrigger::cases())->mapWithKeys(fn ($t) => [$t->value => $t->getReadableName()]))
                    ->native(false)
                    ->default(WebhookTrigger::values()[0])
                    ->required(),
            ])->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->header(fn(Table $table) => view('filament.repository-webhooks-header', [
                'actions' => $table->getHeaderActions(),
                'repository' => $this->record
            ]))
            ->defaultSort('created_at', 'desc')
            ->recordTitleAttribute('name')
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('url')
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('trigger')
                    ->formatStateUsing(fn (WebhookTrigger $state) => $state->getReadableName())
                    ->icon('heroicon-s-bolt')
                    ->badge(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Create Webhook')
                    ->icon('heroicon-m-plus-circle')
                    ->modalHeading('Create a Webhook')
                    ->modalWidth(MaxWidth::Medium)
                    ->createAnother(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading('Edit a Webhook')
                    ->modalWidth(MaxWidth::Medium)
                    ->modalSubmitActionLabel('Save'),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(Notification::make()->title('Webhook has been deleted successfully.')),
            ]);
    }
}
