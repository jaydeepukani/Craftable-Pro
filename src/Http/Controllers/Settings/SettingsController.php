<?php

namespace Brackets\CraftablePro\Http\Controllers\Settings;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Brackets\CraftablePro\Http\Controllers\Controller;
use Brackets\CraftablePro\Http\Requests\Settings\UpdateSettings;
use Brackets\CraftablePro\Models\CraftableProUser;
use Brackets\CraftablePro\Settings\GeneralSettings;
use Brackets\CraftablePro\Translations\TranslationsProcessor;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Laravel\Fortify\Actions\ConfirmTwoFactorAuthentication;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Contracts\TwoFactorConfirmedResponse;
use Laravel\Fortify\Fortify;
use Laravel\Fortify\RecoveryCode;

class SettingsController extends Controller
{
    public function index()
    {
        $this->authorize('craftable-pro.settings.edit');

        $settings = app(GeneralSettings::class);

        $availableRoutes = collect(Route::getRoutes())->filter(function ($route) {
            return isset($route->action['prefix'])
                && ($route->action['prefix'] === '/admin' || $route->action['prefix'] === 'admin')
                && in_array('GET', $route->methods)
                && Str::between($route->uri, '{', '}') === $route->uri
                && ! in_array($route->uri, [
                    'admin',
                    'admin/login',
                    'admin/forgot-password',
                    'admin/verify-email',
                    'admin/confirm-password',
                    'admin/translations/export',
                ]);
        })->map->uri->values()->toArray();

        return Inertia::render('Settings/Index', [
            'generalSettings' => [
                'available_locales' => $settings->available_locales,
                'default_locale' => $settings->default_locale,
                'default_route' => $settings->default_route,
            ],
            'availableRoutes' => $availableRoutes,
        ]);
    }

    public function update(GeneralSettings $settings, UpdateSettings $request)
    {
        $sanitized = $request->validated();

        $settings->available_locales = $sanitized['available_locales'];
        $settings->default_locale = $sanitized['default_locale'];
        $settings->default_route = $sanitized['default_route'];

        $settings->save();

        if ($request->has('available_locales')) {
            Artisan::call('craftable-pro:generate-locale-translations');
            app(TranslationsProcessor::class)->scanTranslations();
            app(TranslationsProcessor::class)->publishTranslations();
        }

        return redirect()->back()->with(['message' => ___('craftable-pro', 'Settings successfully updated')]);
    }

    public function getUserTwoFactorCodes()
    {
        /** @var CraftableProUser $user */
        $user = Auth::user();

        return [
            'twoFactorAuthenticationSetupCode' => ($user->two_factor_secret ? implode(' ', str_split(decrypt($user->two_factor_secret), 4)) : null),
            'twoFactorAuthenticationSetupQrSvg' => ($user->two_factor_secret ? $user->twoFactorQrCodeSvg() : null),
            'twoFactorAuthenticationRecoveryCodes' => json_decode(decrypt(
                $user->two_factor_recovery_codes
            ), true),
        ];
    }

    public function getNewUserTwoFactorCodes()
    {
        /** @var CraftableProUser $user */
        $user = Auth::user();

        $newSetupCode = app(TwoFactorAuthenticationProvider::class)->generateSecretKey();

        $data = [
            'twoFactorAuthenticationSetupCode' => implode(' ', str_split($newSetupCode, 4)),
            'twoFactorAuthenticationSetupQrSvg' => $this->twoFactorQrCodeSvg($newSetupCode, $user),
            'twoFactorAuthenticationRecoveryCodes' => Collection::times(8, function () {
                return RecoveryCode::generate();
            })->all(),
        ];

        $user->forceFill([
            'two_factor_secret_for_refresh' => encrypt($newSetupCode),
            'two_factor_recovery_codes_for_refresh' => encrypt(json_encode($data['twoFactorAuthenticationRecoveryCodes'])),
        ])->save();

        return $data;
    }

    public function twoFactorQrCodeSvg(string $twoFactorSecret, CraftableProUser $user)
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                new SvgImageBackEnd()
            )
        ))->writeString($this->twoFactorQrCodeUrl($twoFactorSecret, $user));

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }

    /**
     * Get the two-factor authentication QR code URL.
     *
     * @return string
     */
    public function twoFactorQrCodeUrl(string $twoFactorSecret, CraftableProUser $user)
    {
        return app(TwoFactorAuthenticationProvider::class)->qrCodeUrl(
            config('app.name'),
            $user->{Fortify::username()},
            $twoFactorSecret
        );
    }

    /**
     * @param Request $request
     * @param ConfirmTwoFactorAuthentication $confirm
     * @return Application|\Illuminate\Foundation\Application|TwoFactorConfirmedResponse|(TwoFactorConfirmedResponse&Application)|mixed
     */
    public function updateNewUserTwoFactorCodes(Request $request, ConfirmTwoFactorAuthentication $confirm)
    {
        /** @var CraftableProUser $user */
        $user = Auth::user();

        DB::transaction(function () use ($user, $confirm, $request) {
            $user->forceFill([
                'two_factor_secret' => $user->two_factor_secret_for_refresh,
                'two_factor_recovery_codes' => $user->two_factor_recovery_codes_for_refresh,
                'two_factor_confirmed_at' => null,
                'two_factor_secret_for_refresh' => null,
                'two_factor_recovery_codes_for_refresh' => null,
            ])->save();

            $user->refresh();

            $confirm($user, $request->input('code'));
        });

        return app(TwoFactorConfirmedResponse::class);
    }
}
