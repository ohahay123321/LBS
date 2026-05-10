<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookRequest;
use App\Models\Category;
use App\Models\Config;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DashboardDemoSeeder extends Seeder
{
    public function run(): void
    {
        // Create Categories
        $categories = ['Fiction', 'Science', 'History', 'Technology', 'Mathematics'];
        foreach ($categories as $cat) {
            Category::firstOrCreate(['name' => $cat]);
        }

        // Create Books
        $books = [
            ['title' => 'The Great Gatsby', 'author' => 'F. Scott Fitzgerald', 'stock' => 5, 'category' => 'Fiction', 'status' => 'AVAILABLE'],
            ['title' => 'A Brief History of Time', 'author' => 'Stephen Hawking', 'stock' => 3, 'category' => 'Science', 'status' => 'BORROWED'],
            ['title' => 'Clean Code', 'author' => 'Robert Martin', 'stock' => 4, 'category' => 'Technology', 'status' => 'AVAILABLE'],
            ['title' => 'The Art of War', 'author' => 'Sun Tzu', 'stock' => 2, 'category' => 'History', 'status' => 'BORROWED'],
            ['title' => 'Calculus Made Easy', 'author' => 'Silvanus Thompson', 'stock' => 6, 'category' => 'Mathematics', 'status' => 'AVAILABLE'],
            ['title' => '1984', 'author' => 'George Orwell', 'stock' => 3, 'category' => 'Fiction', 'status' => 'BORROWED'],
            ['title' => 'The Innovators', 'author' => 'Walter Isaacson', 'stock' => 4, 'category' => 'Technology', 'status' => 'AVAILABLE'],
            ['title' => 'Sapiens', 'author' => 'Yuval Noah Harari', 'stock' => 5, 'category' => 'History', 'status' => 'AVAILABLE'],
            ['title' => 'To Kill a Mockingbird', 'author' => 'Harper Lee', 'stock' => 4, 'category' => 'Fiction', 'status' => 'AVAILABLE'],
            ['title' => 'The Selfish Gene', 'author' => 'Richard Dawkins', 'stock' => 3, 'category' => 'Science', 'status' => 'AVAILABLE'],
        ];
        foreach ($books as $b) {
            Book::firstOrCreate(['title' => $b['title']], $b);
        }

        // Create Student Users
        $students = [
            ['name' => 'John Doe', 'email' => 'john@student.com', 'password' => bcrypt('password'), 'role' => 'USER'],
            ['name' => 'Jane Smith', 'email' => 'jane@student.com', 'password' => bcrypt('password'), 'role' => 'USER'],
            ['name' => 'Bob Wilson', 'email' => 'bob@student.com', 'password' => bcrypt('password'), 'role' => 'USER'],
        ];
        foreach ($students as $s) {
            User::firstOrCreate(['email' => $s['email']], $s);
        }

        // Create Book Requests spanning last 6 months
        $allBooks = Book::all();
        $allStudents = User::where('role', 'USER')->get();
        $now = Carbon::now();

        // Generate requests for each of the last 6 months
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $requestsThisMonth = rand(3, 8);

            for ($j = 0; $j < $requestsThisMonth; $j++) {
                $student = $allStudents->random();
                $book = $allBooks->random();
                $day = rand(1, 28);
                $reqDate = Carbon::create($month->year, $month->month, $day, rand(8, 17), rand(0, 59), 0);
                $statuses = ['PENDING', 'APPROVED', 'DENIED', 'RETURNED'];
                $status = $statuses[array_rand($statuses)];

                $actionDate = null;
                $returnDate = null;
                $fine = 0;
                $finePaid = false;

                if (in_array($status, ['APPROVED', 'RETURNED'])) {
                    $actionDate = $reqDate->copy()->addDays(rand(1, 3));
                    $returnDate = $actionDate->copy()->addDays(3);
                }

                if ($status === 'RETURNED') {
                    // Some returned late, some on time
                    if (rand(0, 1)) {
                        $daysLate = rand(1, 10);
                        $returnDate = $returnDate->addDays($daysLate);
                        $fine = $daysLate * 10;
                    }
                    $finePaid = (bool) rand(0, 1);
                }

                BookRequest::firstOrCreate(
                    [
                        'user_id' => $student->id,
                        'book_id' => $book->id,
                        'req_date' => $reqDate,
                    ],
                    [
                        'student_name' => $student->name,
                        'student_id_num' => 'STU'.str_pad($student->id, 3, '0', STR_PAD_LEFT),
                        'status' => $status,
                        'action_date' => $actionDate,
                        'return_date' => $returnDate,
                        'fine' => $fine,
                        'fine_paid' => $finePaid,
                    ]
                );
            }
        }

        // Set fine rate config
        Config::set('fine_rate', 10);

        // Update book statuses based on requests
        foreach ($allBooks as $book) {
            $activeBorrow = BookRequest::where('book_id', $book->id)
                ->whereIn('status', ['APPROVED'])
                ->exists();
            $book->update(['status' => $activeBorrow ? 'BORROWED' : 'AVAILABLE']);
        }
    }
}
