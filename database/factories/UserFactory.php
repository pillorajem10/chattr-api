<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory for tbl_users.
 *
 * Note: seeded demo users use the password "password" (bcrypt'd).
 * This makes it easy for reviewers to login: use the generated email + "password".
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_fname'    => $this->faker->firstName,
            'user_lname'    => $this->faker->lastName,
            'user_email'    => $this->faker->unique()->safeEmail(),
            'user_bio'      => $this->faker->optional()->sentence(),
            // Demo password for seeded users is "password"
            'user_password' => bcrypt('password'),
            'created_at'    => now(),
            'updated_at'    => now(),
        ];
    }
}
