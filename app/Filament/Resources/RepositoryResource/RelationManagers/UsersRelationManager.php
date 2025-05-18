<?php

namespace App\Filament\Resources\RepositoryResource\RelationManagers;

use App\Enums\Roles;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Actions\AttachAction;
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
                Forms\Components\Select::make('role')
                    ->required()
            ]);
    }
}
