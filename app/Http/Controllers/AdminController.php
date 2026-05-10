<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\BookRequest;
use App\Models\Category;
use App\Models\Config;
use App\Models\Log;
use App\Models\User;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $admin = Auth::guard('admin')->user();

        $stats = [
            'members' => User::where('role', 'USER')->count(),
            'issued' => BookRequest::where('status', 'APPROVED')->count(),
            'available' => Book::where('status', 'AVAILABLE')->count(),
            'pending' => BookRequest::where('status', 'PENDING')->count(),
            'total_fine' => BookRequest::where('fine', '>', 0)->sum('fine'),
            'fine_rate' => Config::get('fine_rate', 10),
        ];

        $recentLogs = Log::orderBy('timestamp', 'desc')->limit(50)->get();
        $pendingRequests = BookRequest::where('status', 'PENDING')->with('book')->get();
        $books = Book::whereDoesntHave('requests', function ($q) {
            $q->where('status', 'PENDING');
        })->orderBy('id', 'desc')->simplePaginate(10, ['*'], 'books_page');
        $categories = Category::orderBy('name')->get();
        $admins = User::where('role', 'ADMIN')->orderBy('id')->simplePaginate(10, ['*'], 'admins_page');
        $students = User::where('role', 'USER')->orderBy('id')->simplePaginate(10, ['*'], 'students_page');
        $issuedBooks = BookRequest::where('status', 'APPROVED')->with('book')->simplePaginate(10, ['*'], 'issued_page');
        $fines = BookRequest::where('fine', '>', 0)->with(['book', 'user'])->orderBy('fine', 'desc')->simplePaginate(10, ['*'], 'fines_page');

        foreach ($fines as $f) {
            if ($f->return_date) {
                $daysLate = max(0, now()->diffInDays($f->return_date, false) * -1);
                $f->days_late = (int) $daysLate;
            }
        }

        // Chart data: Books by status
        $booksByStatus = [
            'labels' => ['Available', 'Borrowed'],
            'data' => [
                Book::where('status', 'AVAILABLE')->count(),
                Book::where('status', 'BORROWED')->count(),
            ],
        ];

        // Chart data: Book requests by month (last 6 months)
        $requestsByMonth = [];
        $monthLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthLabels[] = $month->format('M Y');
            $requestsByMonth[] = BookRequest::whereYear('req_date', $month->year)
                ->whereMonth('req_date', $month->month)
                ->count();
        }

        // Chart data: Fines by month (last 6 months)
        $finesByMonth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $monthFines = BookRequest::where('fine', '>', 0)
                ->whereYear('action_date', $month->year)
                ->whereMonth('action_date', $month->month)
                ->sum('fine');
            $finesByMonth[] = round($monthFines, 2);
        }

        // Chart data: Books by category
        $booksByCategoryLabels = [];
        $booksByCategoryData = [];
        foreach ($categories as $cat) {
            $booksByCategoryLabels[] = $cat->name;
            $booksByCategoryData[] = Book::where('category', $cat->name)->count();
        }

        return view('admin.dashboard', compact(
            'admin', 'stats', 'recentLogs', 'pendingRequests',
            'books', 'categories', 'admins', 'students',
            'issuedBooks', 'fines',
            'booksByStatus', 'monthLabels', 'requestsByMonth',
            'finesByMonth', 'booksByCategoryLabels', 'booksByCategoryData'
        ));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $admin = Auth::guard('admin')->user();
        $admin->update($request->validated());
        Log::create(['description' => 'Admin updated their profile details']);

        return back()->with('success', 'Profile Updated Successfully!');
    }

    public function uploadImage(Request $request)
    {
        $request->validate(['profile_image' => 'required|image|mimes:jpeg,png,gif,webp|max:2048']);
        $admin = Auth::guard('admin')->user();

        if ($admin->profile_image && ! in_array($admin->profile_image, ['default.png', 'imagess.png'])) {
            Storage::disk('public')->delete('profile_images/'.$admin->profile_image);
        }

        $path = $request->file('profile_image')->store('profile_images', 'public');
        $admin->update(['profile_image' => $path]);

        return back()->with('success', 'Profile Picture Updated!');
    }

    public function updateFineRate(Request $request)
    {
        $request->validate(['fine_rate' => 'required|integer|min:1']);
        Config::set('fine_rate', $request->fine_rate);

        return back()->with('success', 'Fine rate updated to PHP '.$request->fine_rate.' per day!');
    }

    public function destroyUser($userId)
    {
        $admin = Auth::guard('admin')->user();
        if ($admin->id == $userId) {
            return back()->with('error', 'You cannot delete yourself.');
        }
        User::destroy($userId);
        Log::create(['description' => 'Admin removed user ID: '.$userId]);

        return back()->with('success', 'User removed!');
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $admin = Auth::guard('admin')->user();
        if (! Hash::check($request->current_password, $admin->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }
        $admin->update(['password' => bcrypt($request->password)]);
        Log::create(['description' => 'Admin changed their password']);

        return back()->with('success', 'Password changed successfully!');
    }
}
