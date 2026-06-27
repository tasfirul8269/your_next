<?php

use Frooxi\Core\Facades\Acl;
use Frooxi\Core\Facades\Core;
use Frooxi\Core\Facades\Menu;
use Frooxi\Core\Facades\SystemConfig;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;
use Stevebauman\Purify\Facades\Purify;

if (! function_exists('core')) {
    /**
     * Core helper.
     *
     * @return Frooxi\Core\Core
     */
    function core()
    {
        return Core::getFacadeRoot();
    }
}

if (! function_exists('menu')) {
    /**
     * Menu helper.
     *
     * @return Frooxi\Core\Menu
     */
    function menu()
    {
        return Menu::getFacadeRoot();
    }
}

if (! function_exists('acl')) {
    /**
     * Acl helper.
     *
     * @return Frooxi\Core\Acl
     */
    function acl()
    {
        return Acl::getFacadeRoot();
    }
}

if (! function_exists('system_config')) {
    /**
     * System Config helper.
     *
     * @return Frooxi\Core\SystemConfig
     */
    function system_config()
    {
        return SystemConfig::getFacadeRoot();
    }
}

if (! function_exists('clean_path')) {
    /**
     * Clean path.
     */
    function clean_path(string $path): string
    {
        return collect(explode('/', $path))
            ->filter(fn ($segment) => ! empty($segment))
            ->join('/');
    }
}

if (! function_exists('clean_content')) {
    /**
     * Clean content.
     */
    function clean_content(string $content): string
    {
        $cleaned = Purify::clean($content);

        $patterns = [
            '/\{\{.*?\}\}/',
            '/\{!!.*?!!\}/',
            '/@(php|if|else|endif|foreach|endforeach|for|endfor|while|endwhile|switch|endswitch|case|break|continue|include|extends|section|endsection|yield|push|endpush|stack|endstack)/',
            '/<\?php.*?\?>/s',
        ];

        foreach ($patterns as $pattern) {
            $cleaned = preg_replace($pattern, '', $cleaned);
        }

        $cleaned = str_replace(
            ['{{', '}}', '{!!', '!!}'],
            ['&#123;&#123;', '&#125;&#125;', '&#123;!!', '!!&#125;'],
            $cleaned
        );

        return $cleaned;
    }
}

if (! function_exists('array_permutation')) {
    function array_permutation($input)
    {
        $results = [];

        foreach ($input as $key => $values) {
            if (empty($values)) {
                continue;
            }

            if (empty($results)) {
                foreach ($values as $value) {
                    $results[] = [$key => $value];
                }
            } else {
                $append = [];

                foreach ($results as &$result) {
                    $result[$key] = array_shift($values);

                    $copy = $result;

                    foreach ($values as $item) {
                        $copy[$key] = $item;
                        $append[] = $copy;
                    }

                    array_unshift($values, $result[$key]);
                }

                $results = array_merge($results, $append);
            }
        }

        return $results;
    }
}

if (! function_exists('frooxi_asset')) {
    /**
     * Frooxi asset helper (deprecated - use nextoutfit_asset instead).
     *
     * @return string
     */
    function frooxi_asset(string $path, ?string $namespace = null)
    {
        return nextoutfit_asset($path, $namespace);
    }
}

if (! function_exists('nextoutfit_asset')) {
    /**
     * Next Outfit asset helper.
     *
     * @return string
     */
    function nextoutfit_asset(string $path, ?string $namespace = null)
    {
        return themes()->url($path, $namespace);
    }
}

if (! function_exists('yournext_asset')) {
    /**
     * Legacy asset helper (deprecated - use frooxi_asset instead).
     *
     * @return string
     */
    function yournext_asset(string $asset, string $package = 'admin')
    {
        return frooxi_asset($asset, $package);
    }
}

if (! function_exists('image_manager')) {
    /**
     * Image manager helper for Intervention Image.
     *
     * @return ImageManager
     */
    function image_manager()
    {
        // Intervention Image v3 expects DriverInterface or fully qualified class name
        return new ImageManager(new GdDriver);
    }
}

if (! function_exists('cloudinary_upload')) {
    /**
     * Upload file to Cloudinary with organized folder structure.
     *
     * @param  UploadedFile  $file
     * @param  string  $folder  Base folder (products, categories, customers, etc.)
     * @param  string|null  $subFolder  Optional subfolder (entity ID, date, etc.)
     * @param  string|null  $filename  Custom filename (optional, auto-generated if null)
     * @param  bool  $convertToWebP  Convert to WebP format (default: true for images)
     * @return string Cloudinary path
     */
    function cloudinary_upload($file, string $folder, ?string $subFolder = null, ?string $filename = null, bool $convertToWebP = true): string
    {
        $disk = config('filesystems.default');
        $basePath = 'next-outfit/'.$folder;

        if ($subFolder) {
            $basePath .= '/'.$subFolder;
        }

        // Generate unique filename if not provided
        if (! $filename) {
            $filename = Str::random(40);
        }

        // Determine extension
        $extension = strtolower($file->getClientOriginalExtension() ?? 'jpg');

        // Convert to WebP for images
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);

        if ($convertToWebP && $isImage) {
            $extension = 'webp';
            $encoded = image_manager()->read($file)->encodeByExtension('webp');
            $fullPath = $basePath.'/'.$filename.'.webp';
            Storage::disk($disk)->put($fullPath, (string) $encoded);
        } else {
            $fullPath = $basePath.'/'.$filename.'.'.$extension;
            Storage::disk($disk)->putFileAs($basePath, $file, $filename.'.'.$extension);
        }

        return $fullPath;
    }
}

if (! function_exists('cloudinary_url')) {
    /**
     * Generate Cloudinary URL DIRECTLY without API calls (FAST!).
     * Bypasses Storage::url() which makes slow API calls.
     *
     * @param  string  $path  Cloudinary path (e.g., 'next-outfit/products/123/image.webp')
     * @param  array  $transformations  Optional Cloudinary transformations
     * @return string Full Cloudinary URL
     */
    function cloudinary_url(string $path, array $transformations = []): string
    {
        $cloudName = env('CLOUDINARY_CLOUD_NAME');

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $resourceType = in_array($extension, ['mp4', 'webm', 'mov', 'avi']) ? 'video' : 'image';

        $baseUrl = "https://res.cloudinary.com/{$cloudName}/{$resourceType}/upload";

        // Add transformations
        if (! empty($transformations)) {
            $transformString = implode(',', array_map(function ($key) use ($transformations) {
                return "{$key}_{$transformations[$key]}";
            }, array_keys($transformations)));
            $baseUrl .= "/{$transformString}";
        }

        // Remove leading slash if present
        $path = ltrim($path, '/');

        return "{$baseUrl}/{$path}";
    }
}
