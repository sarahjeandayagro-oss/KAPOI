<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

$books = DB::table('books')->whereNotNull('cover_image')->limit(10)->get();
echo 'books_with_cover=' . $books->count() . PHP_EOL;
foreach ($books as $book) {
    echo $book->id . '|' . $book->title . '|' . $book->cover_image . PHP_EOL;
}

$files = Storage::disk('public')->files('covers');
echo 'cover_files=' . count($files) . PHP_EOL;
foreach ($files as $file) {
    echo $file . PHP_EOL;
}
