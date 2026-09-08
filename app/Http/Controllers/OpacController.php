<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class OpacController extends Controller
{
    /**
     * Show the OPAC public search page.
     */
    public function index()
    {
        $books = Schema::hasTable('books')
            ? DB::table('books')->whereNull('archived_at')->orderBy('title')->get()
            : collect();

        $categories = Schema::hasTable('books')
            ? DB::table('books')->whereNull('archived_at')->distinct()->pluck('category')
            : collect();

        $totalBooks = $books->count();
        $availableBooks = $books->where('available', '>', 0)->count();

        return view('opac', compact('books', 'categories', 'totalBooks', 'availableBooks'));
    }

    /**
     * Search the library catalog (JSON API).
     */
    public function search(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $type = $request->query('type');
        $availability = $request->query('availability');

        $query = DB::table('books')->whereNull('archived_at');

        // Keyword search across multiple fields
        if ($q !== '') {
            $query->where(function ($inner) use ($q) {
                $inner->where('title', 'like', "%{$q}%")
                    ->orWhere('author', 'like', "%{$q}%")
                    ->orWhere('isbn', 'like', "%{$q}%")
                    ->orWhere('category', 'like', "%{$q}%")
                    ->orWhere('summary', 'like', "%{$q}%")
                    ->orWhere('call_number', 'like', "%{$q}%")
                    ->orWhere('accession_number', 'like', "%{$q}%")
                    ->orWhere('publisher', 'like', "%{$q}%")
                    ->orWhere('place_of_publication', 'like', "%{$q}%");
            });
        }

        // Filter by material type (category)
        if ($type) {
            $query->where('category', $type);
        }

        // Filter by availability
        if ($availability === 'available') {
            $query->where('available', '>', 0);
        } elseif ($availability === 'borrowed') {
            $query->where('available', '<=', 0);
        }

        $results = $query->orderBy('title')->limit(50)->get();

        // Attach cover_url to each result
        $results = $results->map(function ($book) {
            $book->cover_url = $book->cover_image
                ? $this->resolveCoverUrl($book->cover_image)
                : null;
            return $book;
        });

        return response()->json([
            'results' => $results,
            'total' => $results->count(),
            'query' => $q,
            'filters' => [
                'type' => $type,
                'availability' => $availability,
            ],
        ]);
    }

    public function show(int $id)
    {
        $book = Schema::hasTable('books')
            ? DB::table('books')->where('id', $id)->whereNull('archived_at')->first()
            : null;

        abort_if(! $book, 404);

        $book->cover_url = $book->cover_image ? $this->resolveCoverUrl($book->cover_image) : null;
        $book->availability_label = ($book->available ?? 0) > 0 ? 'Available' : 'Unavailable';
        $book->availability_class = ($book->available ?? 0) > 0 ? 'chip-available' : 'chip-borrowed';

        return view('opac-book', compact('book'));
    }

    /**
     * Get all books as JSON for barcode printer.
     */
    public function getAllBooks()
    {
        $books = Schema::hasTable('books')
            ? DB::table('books')->whereNull('archived_at')->orderBy('title')->get()
            : collect();

        return response()->json([
            'books' => $books,
            'total' => $books->count(),
        ]);
    }

    private function resolveCoverUrl(string $coverImage): string
    {
        if (preg_match('/^https?:\/\//', $coverImage)) {
            return $coverImage;
        }

        return '/storage/' . ltrim($coverImage, '/');
    }
}
