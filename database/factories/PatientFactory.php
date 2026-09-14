<?php

namespace Database\Factories;

use App\Models\Facility;
use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    public function definition(): array
    {
        return [
            'facility_id' => Facility::factory(),
            'patient_number' => 'PT-'.now()->format('Y').'-'.Str::upper(Str::random(6)),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'date_of_birth' => fake()->dateTimeBetween('-90 years', '-1 year'),
            'sex' => fake()->randomElement(['male', 'female']),
            'phone' => fake()->numerify('+255#########'),
            'email' => fake()->optional()->safeEmail(),
            'national_id' => fake()->optional()->numerify('##############-##'),
            'nhif_card_number' => fake()->optional()->numerify('##########'),
            'nssf_member_number' => fake()->optional()->bothify('NSSF-#####??'),
            'address' => fake()->address(),
        ];
    }
}
