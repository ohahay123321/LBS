<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookRequest;
use App\Models\Book;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    public function dataTable(Request $request)
    {
        $query = Book::query();

        $recordsTotal = $query->count();

        if ($search = $request->input('search.value')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = $query->count();

        $columns = ['id', 'title', 'author', 'category', 'stock', 'status'];
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'desc');
        if (isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $query->orderBy('id', 'desc');
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $books = $query->skip($start)->take($length)->get();

        $data = [];
        foreach ($books as $b) {
            $data[] = [
                'id' => (string) $b->id,
                'title' => $b->title,
                'author' => $b->author ?? '-',
                'category' => $b->category,
                'stock' => $b->stock ?? 1,
                'status' => $b->status,
                'action' => $this->buildActionColumn($b),
            ];
        }

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data,
        ]);
    }

    private function buildActionColumn($book)
    {
        $csrf = csrf_token();
        $title    = htmlspecialchars($book->title ?? '', ENT_QUOTES, 'UTF-8');
        $author   = htmlspecialchars($book->author ?? '', ENT_QUOTES, 'UTF-8');
        $category = htmlspecialchars($book->category ?? '', ENT_QUOTES, 'UTF-8');

        $editBtn = '<button onclick="editBook(' . $book->id . ', \'' . $title . '\', \'' . $author . '\', \'' . $category . '\', ' . ($book->stock ?? 1) . ')" class="btn-info btn-sm" style="margin-right: 8px;">Edit</button>';

        if ($book->status == 'BORROWED') {
            $removeBtn = '<button disabled style="background:#cbd5e1; cursor:not-allowed; color: #64748b; padding: 8px 16px; border:none; border-radius:8px;">Remove</button>';
        } else {
            $removeBtn = '<form method="POST" action="' . route('admin.books.destroy', $book->id) . '" style="display:inline;" onsubmit="return confirm(\'Delete this book?\')">' .
                '<input type="hidden" name="_token" value="' . $csrf . '">' .
                '<input type="hidden" name="_method" value="DELETE">' .
                '<button type="submit" class="btn-danger btn-sm">Remove</button></form>';
        }

        return $editBtn . $removeBtn;
    }

    public function index()
    {
        return redirect()->route('admin.dashboard');
    }

    public function store(StoreBookRequest $request)
    {
        $validated = $request->validated();

        $imagePath = '';
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('book_covers', 'public');
        }

        Book::create([
            'isbn'     => $validated['isbn'],
            'title'    => $validated['title'],
            'author'   => $validated['author'],
            'stock'    => $validated['stock'],
            'category' => $validated['category'],
            'image'    => $imagePath,
            'status'   => 'AVAILABLE',
        ]);

        Log::create(['description' => 'Admin added book: '.$request->title]);

        return back()->with('success', 'Book added successfully!');
    }

    public function destroy(Book $book)
    {
        if ($book->status == 'BORROWED') {
            return back()->with('error', 'Cannot delete a borrowed book.');
        }

        if ($book->image) {
            Storage::disk('public')->delete($book->image);
        }

        $book->delete();
        Log::create(['description' => 'Admin removed book: '.$book->title]);

        return back()->with('success', 'Book removed!');
    }

    public function create() {}

    public function show(Book $book) {}

    public function edit(Book $book) {}

    public function update(Request $request)
    {
        $request->validate([
            'book_id' => 'required|integer|exists:books,id',
            'title' => 'required|string',
            'author' => 'required|string',
            'stock' => 'required|integer|min:1',
            'category' => 'required|string',
        ]);

        $book = Book::findOrFail($request->book_id);
        $book->update([
            'title' => $request->title,
            'author' => $request->author,
            'stock' => $request->stock,
            'category' => $request->category,
        ]);

        Log::create(['description' => 'Admin updated book: '.$book->title]);

        return back()->with('success', 'Book updated successfully!');
    }
}
