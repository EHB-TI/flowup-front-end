<?php

namespace Database\Factories;

use App\Models\user;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;


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
        $name = $this->faker->name;
        $email = $firstName . '.' . $name . '@student.desiderius.be';

        return [
            'firstName' => $firstName,
            'name' => $name,
            'email' => $email,
            'birthday' => $this->faker->dateTimeThisCentury->format('Y-m-d'),
            'password' => $this->faker->dateTimeThisCentury->format('Y-m-d').$email,
            'role' => 'student',
            'study' => 'TI'
        ];
    }
}
