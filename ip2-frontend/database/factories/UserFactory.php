<?php

namespace Database\Factories;

use App\Models\user;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;


class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = user::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {

        $firstName = $this->faker->firstName;
        $lastName = $this->faker->lastName;
        $email = $firstName . '.' . $lastName . '@student.desiderius.be';

        return [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'birthday' => $this->faker->dateTimeThisCentury->format('Y-m-d'),
            'role' => 'student',
            'study' => 'TI'
        ];
    }
}
