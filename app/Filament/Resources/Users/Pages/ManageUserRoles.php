<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class ManageUserRoles extends Page implements HasForms
{
    use InteractsWithForms;
    use InteractsWithRecord;

    protected static string $resource = UserResource::class;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.resources.users.pages.manage-user-roles';

    /**
     * @var array<string, mixed> | null
     */
    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->mountCanAuthorizeAccess();

        abort_unless(Auth::user()?->can('update', $this->record), 403);

        if ($this->guardAgainstAdminRoleEditing()) {
            return;
        }

        $this->record->loadMissing('roles');

        $this->form->fill([
            'roles' => $this->record->roles->pluck('name')->all(),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make(__('filament.users.assign_roles'))
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                CheckboxList::make('roles')
                                    ->label(__('validation.attributes.roles'))
                                    ->options(fn (): array => Role::query()->pluck('name', 'name')->all())
                                    ->columns(2)
                                    ->searchable()
                                    ->helperText(__('filament.users.roles_helper')),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if ($this->guardAgainstAdminRoleEditing()) {
            return;
        }

        $roles = $data['roles'] ?? [];

        $this->record->syncRoles($roles);

        Notification::make()
            ->title(__('actions.update'))
            ->success()
            ->send();

        $this->redirect(UserResource::getUrl('view', ['record' => $this->record]));
    }

    protected function guardAgainstAdminRoleEditing(): bool
    {
        if (! $this->record instanceof User) {
            return false;
        }

        if (! $this->record->is_admin) {
            return false;
        }

        Notification::make()
            ->title(__('actions.not_allowed'))
            ->body(__('filament.users.super_admin_roles_locked'))
            ->danger()
            ->send();

        $redirect = UserResource::getUrl('view', ['record' => $this->record]);

        if ($redirect) {
            $this->redirect($redirect);
        } else {
            $this->redirect(Auth::user() ? UserResource::getUrl('index') : '/');
        }

        return true;
    }
}
