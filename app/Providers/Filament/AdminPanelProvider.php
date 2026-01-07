<?php

namespace App\Providers\Filament;

use App\Enums\NavigationGroup;
use App\Http\Middleware\EnsureFilamentPanelAccess;
use App\Http\Middleware\SetLocale;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup as FilamentNavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\App;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                SetLocale::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label(fn (): string => 'English')
                    ->icon(Heroicon::OutlinedLanguage)
                    ->toAction(
                        Action::make('switchToEnglish')
                            ->label('English')
                            ->icon('heroicon-o-language')
                            ->visible(fn (): bool => App::getLocale() !== 'en')
                            ->action(function (): void {
                                App::setLocale('en');
                                \Session::put('locale', 'en');
                                redirect(request()->header('Referer') ?? route('filament.admin.pages.dashboard'));
                            }),
                    ),
                MenuItem::make()
                    ->label(fn (): string => 'العربية')
                    ->icon(Heroicon::OutlinedLanguage)
                    ->toAction(
                        Action::make('switchToArabic')
                            ->label('العربية')
                            ->icon('heroicon-o-language')
                            ->visible(fn (): bool => App::getLocale() !== 'ar')
                            ->action(function (): void {
                                App::setLocale('ar');
                                \Session::put('locale', 'ar');
                                redirect(request()->header('Referer') ?? route('filament.admin.pages.dashboard'));
                            }),
                    ),
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureFilamentPanelAccess::class,
            ])
            ->navigationGroups(
                collect(NavigationGroup::cases())
                    ->map(function (NavigationGroup $group): FilamentNavigationGroup {
                        return FilamentNavigationGroup::make()
                            ->label(fn (): string => $group->getLabel());
                    })
                    ->all(),
            )
            ->sidebarCollapsibleOnDesktop()
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->databaseTransactions();
    }
}
