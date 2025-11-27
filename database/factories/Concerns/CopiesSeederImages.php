<?php

namespace Database\Factories\Concerns;

use Illuminate\Support\Facades\File;

trait CopiesSeederImages
{
    /**
     * Copy a random image from the seeder images directory to the storage directory.
     */
    protected function copyRandomSeederImage(string $sourceFolder, string $destinationFolder): string
    {
        $sourceDir = resource_path("seeder_images/{$sourceFolder}");
        $destinationDir = storage_path("app/public/{$destinationFolder}");

        if (! File::exists($destinationDir)) {
            File::makeDirectory($destinationDir, 0755, true);
        }

        $images = File::files($sourceDir);

        if (empty($images)) {
            throw new \RuntimeException("No images found in {$sourceDir}");
        }

        $randomImage = $images[array_rand($images)];
        $extension = $randomImage->getExtension();
        $newFileName = uniqid().'.'.$extension;
        $destinationPath = $destinationDir.'/'.$newFileName;

        File::copy($randomImage->getPathname(), $destinationPath);

        return "{$destinationFolder}/{$newFileName}";
    }
}
