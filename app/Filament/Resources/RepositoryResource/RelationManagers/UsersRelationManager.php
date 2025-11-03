<?php

namespace App\Filament\Resources\RepositoryResource\RelationManagers;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\AttachAction;
use Filament\Forms\Components\Select;
use Filament\Actions\EditAction;
use Filament\Support\Enums\Width;
use Filament\Actions\DetachAction;
use App\Enums\Roles;
use App\Models\User;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use function auth;
use function collect;
use function view;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $title = 'Collaborators';

    public function table(Table $table): Table
    {
        return $table
            ->header(fn(Table $table) => view('filament.repository-collaborators-header', [
                'actions' => $table->getHeaderActions(),
                'repository' => $this->ownerRecord
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
}
