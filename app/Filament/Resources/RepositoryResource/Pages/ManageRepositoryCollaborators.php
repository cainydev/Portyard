<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Enums\Roles;
use App\Filament\Resources\RepositoryResource;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Panel;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Resources\Pages\PageRegistration;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

class ManageRepositoryCollaborators extends ManageRelatedRecords
{
    protected static string $resource = RepositoryResource::class;

    protected static string $relationship = 'users';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-s-users';
    protected static ?string $navigationLabel = 'Collaborators';

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

    public function table(Table $table): Table
    {
        return $table
            ->header(fn(Table $table) => view('filament.repository-collaborators-header', [
                'actions' => $table->getHeaderActions(),
                'repository' => $this->record
            ]))
            ->defaultSort('role')
            ->recordTitleAttribute('name')
            ->paginated(false)
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('role')
                    ->label('Role')
                    ->formatStateUsing(fn(string $state): string => Roles::array()[$state])
                    ->badge(),
                IconColumn::make('accepted')
                    ->default('true')
                    ->boolean()
            ])
            ->headerActions([
                AttachAction::make()
                    ->color('primary')
                    ->label('Invite Collaborator')
                    ->icon('heroicon-s-user-plus')
                    ->modalWidth(Width::Medium)
                    ->modalHeading('Invite Collaborator')
                    ->modalDescription('Select a user to invite to this repository.')
                    ->modalSubmitActionLabel('Invite')
                    ->successNotificationTitle('Invitation sent. You can check the status of the invitation in the Collaborators tab.')
                    ->attachAnother(false)
                    ->form(fn(AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('User')
                            ->placeholder('Search a user')
                            ->required(),
                        Select::make('role')
                            ->label('Role')
                            ->options(collect(Roles::array())->filter(fn($_, $key) => $key !== Roles::Owner->value))
                            ->required(),
                    ])
            ])
            ->recordActions([
                EditAction::make()
                    ->hidden(fn(User $record) => $record->id === auth()->id())
                    ->modalHeading(fn(User $record) => "Edit {$record->name}'s role")
                    ->schema(fn(EditAction $action): array => [
                        Select::make('role')
                            ->hiddenLabel()
                            ->options(collect(Roles::array())->filter(fn($_, $key) => $key !== Roles::Owner->value))
                            ->required(),
                    ])->modalWidth(Width::Medium),
                DetachAction::make()
                    ->label('Revoke')
                    ->modalHeading(fn(User $record) => "Revoke {$record->name}'s role")
                    ->hidden(fn(User $record) => $record->id === auth()->id()),
            ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
