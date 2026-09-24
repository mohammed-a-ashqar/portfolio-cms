<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Media;
use App\Support\Images\ImageProcessor;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Stores uploads and records them in the media table.
 *
 * Conversions are generated eagerly and small: a portfolio grid that ships
 * 4 MB originals is the single biggest thing that makes one feel slow.
 */
final readonly class MediaService
{
    /** @var array<string, array{int, int}> name => [maxWidth, maxHeight] */
    private const CONVERSIONS = [
        'thumb' => [400, 400],
        'medium' => [1024, 1024],
    ];

    public function __construct(
        private ImageProcessor $images,
        private string $disk = 'public',
    ) {}

    public function store(UploadedFile $file, string $collection = 'default', ?Model $attachTo = null): Media
    {
        $directory = trim($collection, '/').'/'.date('Y/m');
        $filename = Str::uuid()->toString().'.'.strtolower($file->getClientOriginalExtension() ?: 'bin');
        $path = $file->storeAs($directory, $filename, $this->disk);

        $dimensions = $this->images->dimensions($file->getRealPath());
        $conversions = $dimensions !== null ? $this->generateConversions($path) : [];

        return Media::create([
            'mediable_type' => $attachTo?->getMorphClass(),
            'mediable_id' => $attachTo?->getKey(),
            'collection' => $collection,
            'disk' => $this->disk,
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'width' => $dimensions['width'] ?? null,
            'height' => $dimensions['height'] ?? null,
            'conversions' => $conversions,
            'uploaded_by' => Auth::id(),
        ]);
    }

    /** Convenience for single-image columns (cover_image) that need no media row. */
    public function storePath(UploadedFile $file, string $collection = 'default'): string
    {
        $directory = trim($collection, '/').'/'.date('Y/m');
        $filename = Str::uuid()->toString().'.'.strtolower($file->getClientOriginalExtension() ?: 'bin');

        return $file->storeAs($directory, $filename, $this->disk);
    }

    public function delete(Media $media): bool
    {
        $disk = Storage::disk($media->disk);

        foreach (array_merge([$media->path], array_values($media->conversions ?? [])) as $path) {
            $disk->delete($path);
        }

        return (bool) $media->delete();
    }

    public function deletePath(?string $path): void
    {
        if ($path) {
            Storage::disk($this->disk)->delete($path);
        }
    }

    /** @return array<string, string> name => stored path */
    private function generateConversions(string $originalPath): array
    {
        $disk = Storage::disk($this->disk);
        $absolute = $disk->path($originalPath);
        $conversions = [];

        foreach (self::CONVERSIONS as $name => [$maxWidth, $maxHeight]) {
            $target = $this->conversionPath($originalPath, $name);

            if ($this->images->fit($absolute, $disk->path($target), $maxWidth, $maxHeight)) {
                $conversions[$name] = $target;
            }
        }

        return $conversions;
    }

    private function conversionPath(string $path, string $name): string
    {
        $info = pathinfo($path);

        return "{$info['dirname']}/conversions/{$info['filename']}-{$name}.{$info['extension']}";
    }
}
