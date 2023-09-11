<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = User::class;
    protected static ?string $slug = 'settings/users';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Users';
    // protected static ?string $recordTitleAttribute = 'name';
    protected static ?int $navigationSort = 1;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any'
        ];
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    TextInput::make('password')
                        ->password()
                        ->required()
                        ->dehydrated(fn($state) => filled($state))
                        ->dehydrateStateUsing(fn($state) => Hash::make($state))
                        ->visibleOn('create'),
                    TextInput::make('passwordConfirmation')
                        ->password()
                        ->visibleOn('create')
                        ->required(fn(Page $livewire) => $livewire instanceof CreateRecord)
                        ->same('password')
                        ->dehydrated(false),
                    Select::make('roles')
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->searchable()
                        ->preload(),
                    Toggle::make('verified')
                        ->onIcon('heroicon-s-bolt')
                        ->offIcon('heroicon-s-user')
                        ->onColor('success')
                        ->offColor('danger')
                        ->visibleOn('edit')
                        ->label(fn($state) => $state == true ? 'verified' : 'not verified')
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('name')
                            ->searchable()
                            ->sortable(),
                        TagsColumn::make('roles.name')
                            ->searchable()
                    ]),
                    Stack::make([
                        TextColumn::make('email')
                            ->searchable()
                            ->sortable()
                            ->alignLeft()
                            ->icon(fn($record) => $record->email_verified_at !== null ? '' : 'heroicon-o-shield-exclamation')
                            ->iconPosition('before')
                            ->tooltip(fn($record) => $record->email_verified_at !== null ? 'verified' : 'not verified'),
                        TextColumn::make('email_verified_at')
                            ->sortable(false),
                    ])
                ]),
            ])
            ->filters([
                Filter::make('name'),
                Filter::make('email'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}