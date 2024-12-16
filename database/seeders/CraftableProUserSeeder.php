<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CraftableProUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = config('craftable-pro.craftable_pro_user_model')::factory()
            ->count(20)
            ->create();

        $users->take(5)->each(function (CraftableProUser $user) {

            $fullName = $user->first_name . ' ' . $user->last_name;

            $user->addMediaFromUrl("https://i.pravatar.cc/400")
                ->withCustomProperties([
                    "name" => $fullName,
                    "extension" => "jpg",
                    "size" => rand(10, 100) * 1024,
                    "alt" => $fullName,
                ])
                ->toMediaCollection('avatar', config('craftable-pro.default_media_disk_name'));
        });

        $users->shuffle()->take(10)->each(function ($user) {
            $user->assignRole('Administrator');
        });
    }
}
