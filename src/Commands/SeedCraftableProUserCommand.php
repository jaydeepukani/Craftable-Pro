<?php

namespace Brackets\CraftablePro\Commands;

use Brackets\CraftablePro\Settings\GeneralSettings;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

class SeedCraftableProUserCommand extends Command
{
    public $signature = 'craftable-pro:create-admin-user';

    public $description = 'Create administrator access';

    public function handle(): int
    {
        // TODO consider alerting in production

        $default = 'administrator@brackets.sk';
        $email = $this->option('no-interaction') ? $default : $this->components->ask('Creating an administrator account. Enter an email address (login): ', $default);

        $password = Str::random(12);

        $user = config('craftable-pro.craftable_pro_user_model')::updateOrCreate([
            'email' => $email,
        ], [
            'first_name' => 'Administrator',
            'last_name' => 'Administrator',
            'email' => $email,
            'password' => bcrypt($password),
            'locale' => app(GeneralSettings::class)->default_locale,
        ]);

        $user->markEmailAsVerified();

        $role = Role::query()->where('guard_name', 'craftable-pro')->where('name', 'Administrator')->first();

        $user->assignRole($role ?? 1);

        $this->components->info("Administrator account was created with credentials (login/password): <fg=green;options=bold>$email</> / <fg=blue;options=bold>$password</> - we recommend to change the password.");

        return self::SUCCESS;
    }
}
