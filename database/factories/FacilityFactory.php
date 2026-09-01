<?php

namespace Database\Factories;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Facility>
 */
class FacilityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->city().' Hospital',
            'code' => Str::upper(Str::random(6)),
            'type' => fake()->randomElement(['national', 'regional', 'district', 'dispensary']),
            'phone' => fake()->numerify('+255#########'),
            'email' => fake()->companyEmail(),
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }
}
