<?php

namespace Database\Factories;

use App\Models\Book;
use App\Models\BookRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookRequestFactory extends Factory
{
    protected $model = BookRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'book_id' => Book::factory(),
            'student_name' => $this->faker->name(),
            'student_id_num' => 'STU'.$this->faker->randomNumber(3),
            'status' => 'PENDING',
            'req_date' => now(),
            'action_date' => null,
            'return_date' => null,
            'fine' => 0,
            'fine_paid' => false,
        ];
    }

    public function approved()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'APPROVED',
            'action_date' => now(),
            'return_date' => now()->addDays(3),
        ]);
    }

    public function returned()
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'RETURNED',
            'action_date' => now()->subDays(5),
            'return_date' => now(),
        ]);
    }
}
