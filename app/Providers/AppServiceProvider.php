<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use App\Models\Registrar\User as RegistrarUser;
use Laravel\Passport\Passport;
use Laravel\Passkeys\Contracts\PasskeyUser;
use Laravel\Passkeys\Passkey;
use Laravel\Passkeys\Passkeys;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::shouldBeStrict(!$this->app->isProduction());

        Passport::enablePasswordGrant();

        Passport::tokensExpireIn(now()->addHours(3));
        Passport::refreshTokensExpireIn(now()->addHours(8));

        Passkeys::useUserModel(RegistrarUser::class);
        Passkeys::authorizeLoginUsing(function (Request $request, PasskeyUser $user, Passkey $passkey): bool {
            return $user instanceof RegistrarUser && $user->status;
        });
    }
}
