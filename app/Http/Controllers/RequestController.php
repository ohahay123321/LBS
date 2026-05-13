<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookRequest;
use App\Models\Log;
use App\Notifications\BookReturned;
use App\Notifications\RequestApproved;
use App\Notifications\RequestDenied;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestController extends Controller
{
    public function index()
    {
        $requests = BookRequest::with(['user', 'book', 'admin'])->orderBy('req_date', 'desc')->get();

        return view('admin.requests.index', compact('requests'));
    }

    public function approve($reqId)
    {
        $request = BookRequest::with('book')->findOrFail($reqId);
        $request->update([
            'status'      => 'APPROVED',
            'action_date' => now(),
            'return_date' => now()->addDays(3),
            'approved_by' => Auth::guard('admin')->id(),
        ]);
        Book::where('id', $request->book_id)->decrement('stock');
        Book::where('id', $request->book_id)->update(['status' => 'BORROWED']);

        Log::create(['description' => 'Admin approved book request ID: ' . $reqId . ' for book: ' . ($request->book->title ?? $request->book_id)]);

        if ($request->user) {
            $request->user->notify(new RequestApproved($request));
        }

        return back()->with('success', 'Request approved!');
    }

    public function deny($reqId)
    {
        $request = BookRequest::with('book')->findOrFail($reqId);
        $request->update([
            'status'      => 'DENIED',
            'action_date' => now(),
            'approved_by' => Auth::guard('admin')->id(),
        ]);

        Log::create(['description' => 'Admin denied book request ID: ' . $reqId . ' for book: ' . ($request->book->title ?? $request->book_id)]);

        if ($request->user) {
            $request->user->notify(new RequestDenied($request));
        }

        return back()->with('success', 'Request denied.');
    }

    public function returnBook($reqId)
    {
        $request = BookRequest::with('book')->findOrFail($reqId);

        // Calculate fine: days overdue × fine rate per day
        $fine = 0;
        if ($request->return_date && now()->gt($request->return_date)) {
            $daysLate = (int) now()->diffInDays($request->return_date);
            $fineRate = \App\Models\Config::get('fine_rate', 10);
            $fine = $daysLate * $fineRate;
        }

        $request->update([
            'status'      => 'RETURNED',
            'action_date' => now(),
            'approved_by' => Auth::guard('admin')->id(),
            'fine'        => $fine,
        ]);
        Book::where('id', $request->book_id)->increment('stock');
        Book::where('id', $request->book_id)->update(['status' => 'AVAILABLE']);

        Log::create(['description' => 'Admin marked book returned for request ID: ' . $reqId . ($fine > 0 ? ' | Fine: PHP ' . $fine : '')]);

        if ($request->user) {
            $request->user->notify(new BookReturned($request));
        }

        return back()->with('success', 'Book marked as returned!');
    }

    public function payFine($reqId)
    {
        $request = BookRequest::findOrFail($reqId);
        $request->update(['fine_paid' => true]);
        Log::create(['description' => 'Fine paid for request ID: '.$reqId]);

        return back()->with('success', 'Fine marked as paid!');
    }

    public function create() {}

    public function store(Request $request) {}

    public function show(BookRequest $bookRequest) {}

    public function edit(BookRequest $bookRequest) {}

    public function update(Request $request, BookRequest $bookRequest) {}

    public function destroy(BookRequest $bookRequest) {}
}
