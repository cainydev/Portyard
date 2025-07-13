<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Enums\Roles;
use App\Filament\Resources\RepositoryResource;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;

class ManageRepositoryCollaborators extends ManageRelatedRecords
{
    protected static string $resource = RepositoryResource::class;

    protected static string $relationship = 'users';
    protected static ?string $navigationIcon = 'heroicon-s-users';
    protected static ?string $navigationLabel = 'Collaborators';

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
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('role')
                    ->label('Role')
                    ->formatStateUsing(fn(string $state): string => Roles::array()[$state])
                    ->badge(),
                Tables\Columns\IconColumn::make('accepted')
                    ->default('true')
                    ->boolean()
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->color('primary')
                    ->label('Invite Collaborator')
                    ->icon('heroicon-s-user-plus')
                    ->modalWidth(MaxWidth::Medium)
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
                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options(collect(Roles::array())->filter(fn($_, $key) => $key !== Roles::Owner->value))
                            ->required(),
                    ])
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->hidden(fn(User $record) => $record->id === auth()->id())
                    ->modalHeading(fn(User $record) => "Edit {$record->name}'s role")
                    ->form(fn(Tables\Actions\EditAction $action): array => [
                        Forms\Components\Select::make('role')
                            ->hiddenLabel()
                            ->options(collect(Roles::array())->filter(fn($_, $key) => $key !== Roles::Owner->value))
                            ->required(),
                    ])->modalWidth(MaxWidth::Medium),
                Tables\Actions\DetachAction::make()
                    ->label('Revoke')
                    ->modalHeading(fn(User $record) => "Revoke {$record->name}'s role")
                    ->hidden(fn(User $record) => $record->id === auth()->id()),
            ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }
}
