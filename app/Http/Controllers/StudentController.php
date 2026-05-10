<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookRequest;
use App\Models\Category;
use App\Models\User;
use App\Notifications\NewBookRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = Auth::guard('student')->user();

        $filter = $request->query('filter', 'available');
        $search = $request->query('search', '');
        $category = $request->query('category', '');

        $booksQuery = Book::query();

        if ($filter == 'available') {
            $booksQuery->where('status', 'AVAILABLE')->where('stock', '>', 0);
        } elseif ($filter == 'borrowed') {
            $booksQuery->where('status', 'BORROWED');
        } elseif ($filter == 'outofstock') {
            $booksQuery->where(function ($q) {
                $q->where('status', 'AVAILABLE')->where('stock', 0);
            });
        }

        if (! empty($search)) {
            $booksQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('author', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if (! empty($category)) {
            $booksQuery->where('category', $category);
        }

        $books = $booksQuery->orderBy('id', 'desc')->get();
        $categories = Category::orderBy('name')->get();

        $myRequests = BookRequest::where('user_id', $user->id)
            ->with(['book', 'admin'])
            ->orderBy('req_date', 'desc')
            ->get();

        return view('student.dashboard', compact('books', 'myRequests', 'filter', 'search', 'category', 'categories'));
    }

    public function requestBook(Request $request)
    {
        $user = Auth::guard('student')->user();

        $existing = BookRequest::where('user_id', $user->id)
            ->where('book_id', $request->book_id)
            ->whereIn('status', ['PENDING', 'APPROVED'])
            ->exists();

        if ($existing) {
            return back()->with('error', 'You already have a pending or approved request for this book!');
        }

        $bookRequest = BookRequest::create([
            'book_id' => $request->book_id,
            'user_id' => $user->id,
            'student_name' => $request->student_name,
            'student_id_num' => $request->student_id_num,
            'status' => 'PENDING',
        ]);

        $bookRequest->load('book');

        $admins = User::where('role', 'ADMIN')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewBookRequest($bookRequest));
        }

        return back()->with('success', 'Request Sent!');
    }

    public function receipt($id)
    {
        $user = Auth::guard('student')->user();

        $request = BookRequest::with(['book', 'user', 'admin'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return view('student.receipt', compact('request'));
    }
}
