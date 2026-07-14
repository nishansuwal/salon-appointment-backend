<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use File;

trait HandlesImagesTrait
{
    /**
     * Handle the image upload process.
     *
     * @param Request $request
     * @param string $fileName
     * @param string $directory
     * @param string|null $oldImage
     * @return string|null
     */
    public function handleImageUpload(Request $request, string $fileName, string $directory, string $oldImage = null)
    {
        if ($request->hasFile($fileName)) {
            $current = Carbon::now()->format('YmdHs');
            $newImageName = $current . '_' . $request->file($fileName)->getClientOriginalName();

            // Store the image in the specified directory within storage/app/public
            $newImagePath = $request->file($fileName)->storeAs($directory, $newImageName, 'public');

            if ($oldImage) {
                Log::info($oldImage);
                $this->deleteImage($directory, $oldImage);
            }
            return $newImagePath;
        }

        return $oldImage;
    }

    /**
     * Delete an image from the storage.
     *
     * @param string $directory
     * @param string $imageName
     * @return void
     */
    public function deleteImage(string $directory, string $imageName)
    {
        Log::info('Attempting to delete image at path: ' . $imageName);
        if (Storage::disk('public')->exists($imageName)) {
            Storage::disk('public')->delete($imageName);
        }
    }
}
