<?php

namespace Brackets\CraftablePro\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class CraftableProUserFactory extends Factory
{
    public function modelName()
    {
        return config('craftable-pro.craftable_pro_user_model');
    }

    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'email' => $this->faker->email,
            'password' => Hash::make($this->faker->password),
            'email_verified_at' => $this->faker->dateTime,
            'remember_token' => $this->faker->md5,
            'locale' => $this->faker->randomElement(['en', 'de', 'fr']),
        ];
    }
}
