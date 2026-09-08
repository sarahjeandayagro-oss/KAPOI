<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorSVG;

class BarcodeController extends Controller
{
    /**
     * Generate barcode as SVG image for books
     */
    public function generateBook(string $barcode)
    {
        try {
            $generator = new BarcodeGeneratorSVG();
            $barcode = trim($barcode);

            if (empty($barcode)) {
                throw new \Exception('Barcode is empty');
            }

            $svg = $generator->getBarcode($barcode, BarcodeGeneratorSVG::TYPE_CODE_128);

            return response($svg, 200, [
                'Content-Type' => 'image/svg+xml',
                'Cache-Control' => 'public, max-age=604800', // Cache for 1 week
            ]);
        } catch (\Exception $e) {
            \Log::error('Barcode generation failed: ' . $e->getMessage());
            // Return SVG error placeholder
            return response('<svg xmlns="http://www.w3.org/2000/svg" width="100" height="50"><text x="5" y="20" font-size="10" fill="red">Error</text></svg>', 200, [
                'Content-Type' => 'image/svg+xml',
            ]);
        }
    }

    /**
     * Generate barcode as HTML for display/printing
     */
    public function generateBookHtml(string $barcode): string
    {
        try {
            $generator = new BarcodeGeneratorHTML();
            $barcode = trim($barcode);

            return $generator->getBarcode($barcode, BarcodeGeneratorHTML::TYPE_CODE_128);
        } catch (\Exception $e) {
            return '<span style="color: red; font-size: 12px;">Invalid barcode</span>';
        }
    }

    /**
     * Get barcode image URL for a book barcode
     */
    public function bookBarcodeUrl(string $barcode): string
    {
        return route('barcode.book.png', ['barcode' => $barcode]);
    }
}
