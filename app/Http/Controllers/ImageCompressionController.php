<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Log;

class ImageCompressionController extends Controller
{
    public function index()
    {
        return view('compression.index');
    }

    public function compress(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // Max 5MB
            'quality' => 'nullable|integer|min:10|max:100',
        ]);

        try {
            $file = $request->file('image');
            $originalSize = $file->getSize();
            $originalMime = $file->getMimeType();

            // Create new manager instance with desired driver
            $manager = new ImageManager(new Driver());

            // Read image from file system
            $image = $manager->read($file->getPathname());

            // Compression logic
            // We'll standardise functionality here. 
            // For now, let's just re-encode with specified quality to reduce size.
            // If the image is massive, we might want to resize it safely too, but let's stick to compression first.

            $quality = $request->input('quality', 80);

            // Encode the image
            // We use the same format as input if possible, or fallback to jpeg
            $encoded = $image->toJpeg($quality);
            if ($originalMime == 'image/png') {
                $encoded = $image->toPng(); // PNG compression is different, usually lossless or indexed.
                // For significant reduction on PNG, we might need to change implementation or convert to lossy format like WebP/JPEG
                // Let's force Convert to WebP if user agrees? Or just Keep original format?
                // For this "demo", let's offer WebP conversion as it's efficient.
                // Actually, let's stick to simple quality adjustment for JPEGs and simple read/write for others to strip metadata.
            }

            // Let's simplify: Convert everything to WebP for maximum compression if it's for web use?
            // Or respect input. Let's respect input but default to JPEG 80% if it's a photo.

            // Better approach for "Compress":
            // 1. Resize if too big (> 2000px width)
            // 2. Reduce quality

            if ($image->width() > 2000) {
                $image->scale(width: 2000);
            }

            $encoded = $image->toJpeg($quality);

            // If original was clear PNG/WebP, maybe we should keep it.
            // For now, let's just return a JPEG preview for simplicity in this V1.

            $compressedData = (string) $encoded;
            $compressedSize = strlen($compressedData);
            $base64 = base64_encode($compressedData);

            // Store in DB
            $tempImage = \App\Models\TemporaryImage::create([
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => 'image/jpeg', // We are forcing JPEG for now
                'image_data' => $base64,
                'original_size' => $originalSize,
                'compressed_size' => $compressedSize,
            ]);

            // Opportunistic cleanup (1% chance or just run it? Let's run it)
            // ideally this should be a scheduled job, but for now this ensures cleanup on serverless
            // without external cron if traffic is low.
            $this->cleanup();

            return response()->json([
                'success' => true,
                'original_size' => $this->formatBytes($originalSize),
                'compressed_size' => $this->formatBytes($compressedSize),
                'saved_bytes' => $this->formatBytes($originalSize - $compressedSize),
                'saved_percent' => round((($originalSize - $compressedSize) / $originalSize) * 100, 2) . '%',
                'image_base64' => 'data:image/jpeg;base64,' . $base64,
                'mime_type' => 'image/jpeg',
                'download_url' => route('tools.compression.download', $tempImage->id),
            ]);

        } catch (\Exception $e) {
            Log::error('Image compression failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Image compression failed. ' . $e->getMessage(),
            ], 500);
        }
    }

    public function download($id)
    {
        $image = \App\Models\TemporaryImage::findOrFail($id);

        $data = base64_decode($image->image_data);

        return response($data)
            ->header('Content-Type', $image->mime_type)
            ->header('Content-Disposition', 'attachment; filename="compressed-' . $image->file_name . '"');
    }

    private function cleanup()
    {
        // Delete images older than 1 hour
        \App\Models\TemporaryImage::where('created_at', '<', now()->subHour())->delete();
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Calculate bytes /= (1 << (10 * $pow));
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
