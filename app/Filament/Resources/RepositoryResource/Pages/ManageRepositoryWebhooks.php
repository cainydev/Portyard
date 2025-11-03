<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Enums\WebhookTrigger;
use App\Filament\Resources\RepositoryResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Panel;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

class ManageRepositoryWebhooks extends ManageRelatedRecords
{
    protected static string $resource = RepositoryResource::class;
    protected static ?string $navigationLabel = 'Webhooks';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-s-cloud-arrow-up';
    protected static string $relationship = 'webhooks';

    public static function route(string $path): PageRegistration
    {
        return new PageRegistration(
            page: static::class,
            route: fn (Panel $panel): Route => RouteFacade::get($path, static::class)
                ->middleware(static::getRouteMiddleware($panel))
                ->withoutMiddleware(static::getWithoutRouteMiddleware($panel))
                ->scopeBindings(),
        );
    }

    public function getBreadcrumbs(): array
    {
        return [static::getResource()::getPluralModelLabel(), $this->getTitle(), static::getNavigationLabel()];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->path;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->hint('just for you')
                    ->required()
                    ->maxLength(255),
                TextInput::make('url')
                    ->url()
                    ->hint('include http(s)')
                    ->required(),
                Select::make('trigger')
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
                TextColumn::make('name'),
                TextColumn::make('url')
                    ->weight(FontWeight::Bold),
                TextColumn::make('trigger')
                    ->formatStateUsing(fn (WebhookTrigger $state) => $state->getReadableName())
                    ->icon('heroicon-s-bolt')
                    ->badge(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Create Webhook')
                    ->icon('heroicon-m-plus-circle')
                    ->modalHeading('Create a Webhook')
                    ->modalWidth(Width::Medium)
                    ->createAnother(false),
            ])
            ->recordActions([
                EditAction::make()
                    ->modalHeading('Edit a Webhook')
                    ->modalWidth(Width::Medium)
                    ->modalSubmitActionLabel('Save'),
                DeleteAction::make()
                    ->successNotification(Notification::make()->title('Webhook has been deleted successfully.')),
            ]);
    }
}
