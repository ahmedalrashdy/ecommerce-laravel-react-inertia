<?php

namespace App\Filament\Resources\Users\Tables;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use App\Notifications\Auth\ForcedPasswordReset;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('roles'))
            ->columns([
                ImageColumn::make('avatar')
                    ->label(__('validation.attributes.avatar'))
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name ?? '').'&background=random')
                    ->size(50),

                TextColumn::make('name')
                    ->label(__('validation.attributes.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label(__('validation.attributes.email'))
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                TextColumn::make('gender')
                    ->label(__('validation.attributes.gender'))
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'male' => 'ذكر',
                        'female' => 'أنثى',
                        default => '—',
                    })
                    ->toggleable(),

                TextColumn::make('roles.name')
                    ->label(__('validation.attributes.roles'))
                    ->badge()
                    ->separator(',')
                    ->color('info')
                    ->placeholder('—')
                    ->toggleable(),

                IconColumn::make('is_admin')
                    ->label(__('validation.attributes.is_admin'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                IconColumn::make('is_active')
                    ->label(__('validation.attributes.is_active'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('email_verified_at')
                    ->label(__('validation.attributes.email_verified_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->placeholder('—'),

                TextColumn::make('created_at')
                    ->label(__('validation.attributes.created_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('validation.attributes.updated_at'))
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),

                TernaryFilter::make('is_admin')
                    ->label(__('validation.attributes.is_admin'))
                    ->placeholder(__('filament.filters.all'))
                    ->trueLabel(__('filament.filters.admin_only'))
                    ->falseLabel(__('filament.filters.non_admin_only')),

                TernaryFilter::make('is_active')
                    ->label(__('validation.attributes.is_active'))
                    ->placeholder(__('filament.filters.all'))
                    ->trueLabel(__('filament.filters.active_only'))
                    ->falseLabel(__('filament.filters.inactive_only')),

                SelectFilter::make('roles')
                    ->label(__('validation.attributes.roles'))
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->label(__('actions.view')),
                EditAction::make()
                    ->label(__('actions.edit')),
                Action::make('manage_roles')
                    ->label(__('actions.manage_roles'))
                    ->icon('heroicon-o-shield-check')
                    ->url(fn (User $record): string => UserResource::getUrl('roles', ['record' => $record]))
                    ->visible(fn (User $record): bool => ! $record->is_admin && (bool) Auth::user()?->can('update', $record)),
                Action::make('toggle_active')
                    ->label(fn (User $record): string => $record->is_active ? __('actions.disable') : __('actions.enable'))
                    ->icon(fn (User $record): string => $record->is_active ? 'heroicon-o-user-minus' : 'heroicon-o-user-plus')
                    ->requiresConfirmation(fn (User $record): bool => ! ($record->is_admin && $record->is_active))
                    ->hidden(fn (User $record): bool => ($record->is_admin && $record->is_active))
                    ->action(function (User $record): void {
                        if ($record->is_admin && $record->is_active) {
                            Notification::make()
                                ->title(__('actions.not_allowed'))
                                ->body(__('actions.admin_cannot_deactivate'))
                                ->danger()
                                ->send();

                            return;
                        } else {

                            $record->update([
                                'is_active' => ! $record->is_active,
                            ]);

                            Notification::make()
                                ->title(__('actions.update'))
                                ->body($record->is_active ? __('filament.filters.active_only') : __('filament.filters.inactive_only'))
                                ->success()
                                ->send();
                        }
                    }),
                Action::make('force_password_reset')
                    ->label(__('actions.force_password_reset'))
                    ->icon('heroicon-o-key')
                    ->requiresConfirmation()
                    ->disabled(fn (User $record): bool => $record->reset_password_required)
                    ->action(function (User $record): void {
                        $record->update([
                            'reset_password_required' => true,
                        ]);

                        $token = Password::broker()->createToken($record);

                        $record->notify(new ForcedPasswordReset($token));

                        Notification::make()
                            ->title(__('actions.update'))
                            ->body(__('actions.force_password_reset'))
                            ->success()
                            ->send();
                    }),
                Action::make('toggle_super_admin')
                    ->label(fn (User $record): string => $record->is_admin ? __('actions.revoke_super_admin') : __('actions.make_super_admin'))
                    ->icon(fn (User $record): string => $record->is_admin ? 'heroicon-o-shield-exclamation' : 'heroicon-o-shield-check')
                    ->color(fn (User $record): string => $record->is_admin ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->visible(fn (User $record): bool => (bool) Auth::user()?->is_admin && Auth::id() !== $record->id)
                    ->action(function (User $record): void {
                        $actor = Auth::user();

                        if (! $actor?->is_admin) {
                            Notification::make()
                                ->title(__('actions.not_allowed'))
                                ->danger()
                                ->send();

                            return;
                        }

                        if ($actor->is($record)) {
                            Notification::make()
                                ->title(__('actions.not_allowed'))
                                ->body(__('filament.users.cannot_change_own_super_admin'))
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->update([
                            'is_admin' => ! $record->is_admin,
                        ]);

                        Notification::make()
                            ->title(__('actions.update'))
                            ->body($record->is_admin ? __('filament.users.super_admin_assigned') : __('filament.users.super_admin_revoked'))
                            ->success()
                            ->send();
                    }),
                DeleteAction::make()
                    ->label(__('actions.delete')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->label(__('actions.delete')),
                    ForceDeleteBulkAction::make()
                        ->label(__('actions.force_delete')),
                    RestoreBulkAction::make()
                        ->label(__('actions.restore')),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
