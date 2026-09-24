<?php

declare(strict_types=1);

namespace App\Support\Images;

use GdImage;
use RuntimeException;

/**
 * Thin GD wrapper for the two things this CMS actually needs: read the
 * dimensions of an upload, and produce a smaller copy.
 *
 * Deliberately not Intervention Image — that pulls a dependency tree for
 * features a portfolio never uses, and GD is already required by the app.
 */
final class ImageProcessor
{
    public function __construct(private readonly int $jpegQuality = 82) {}

    /** @return array{width: int, height: int, mime: string}|null */
    public function dimensions(string $path): ?array
    {
        $info = @getimagesize($path);

        if ($info === false) {
            return null;
        }

        return ['width' => $info[0], 'height' => $info[1], 'mime' => $info['mime']];
    }

    /**
     * Resize to fit inside the box, preserving aspect ratio.
     * Never upscales — enlarging a small upload only makes it blurry.
     */
    public function fit(string $sourcePath, string $destinationPath, int $maxWidth, int $maxHeight): bool
    {
        $info = $this->dimensions($sourcePath);

        if ($info === null) {
            return false;
        }

        $source = $this->read($sourcePath, $info['mime']);

        if (! $source instanceof GdImage) {
            return false;
        }

        [$width, $height] = [$info['width'], $info['height']];
        $ratio = min($maxWidth / $width, $maxHeight / $height, 1);

        $targetWidth = max(1, (int) round($width * $ratio));
        $targetHeight = max(1, (int) round($height * $ratio));

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

        // Keep transparency for PNG/WebP instead of filling it black.
        if (in_array($info['mime'], ['image/png', 'image/webp'], true)) {
            imagealphablending($canvas, false);
            imagesavealpha($canvas, true);
            imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
        }

        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        $written = $this->write($canvas, $destinationPath, $info['mime']);

        imagedestroy($canvas);
        imagedestroy($source);

        return $written;
    }

    private function read(string $path, string $mime): ?GdImage
    {
        $image = match ($mime) {
            'image/jpeg' => @imagecreatefromjpeg($path),
            'image/png' => @imagecreatefrompng($path),
            'image/gif' => @imagecreatefromgif($path),
            'image/webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : false,
            default => false,
        };

        return $image instanceof GdImage ? $image : null;
    }

    private function write(GdImage $image, string $path, string $mime): bool
    {
        $directory = dirname($path);

        if (! is_dir($directory) && ! mkdir($directory, 0o755, true) && ! is_dir($directory)) {
            throw new RuntimeException("Could not create directory [{$directory}].");
        }

        return match ($mime) {
            'image/jpeg' => imagejpeg($image, $path, $this->jpegQuality),
            'image/png' => imagepng($image, $path, 6),
            'image/gif' => imagegif($image, $path),
            'image/webp' => function_exists('imagewebp') && imagewebp($image, $path, $this->jpegQuality),
            default => false,
        };
    }
}
