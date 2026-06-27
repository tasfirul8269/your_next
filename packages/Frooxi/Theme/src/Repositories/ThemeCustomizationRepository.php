<?php

namespace Frooxi\Theme\Repositories;

use Frooxi\Core\Eloquent\Repository;
use Frooxi\Theme\Contracts\ThemeCustomization;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Stevebauman\Purify\Facades\Purify;

class ThemeCustomizationRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return ThemeCustomization::class;
    }

    /**
     * Update the specified theme
     *
     * @param  array  $data
     * @param  int  $id
     */
    public function update($data, $id): ThemeCustomization
    {
        $locale = core()->getRequestedLocaleCode();

        if ($data['type'] == 'static_content') {
            $config = [
                'HTML.Allowed' => null,
                'HTML.ForbiddenElements' => 'script,iframe,form',
                'CSS.AllowedProperties' => null,
            ];

            $data[$locale]['options']['html'] = Purify::config($config)->clean($data[$locale]['options']['html']);

            $data[$locale]['options']['css'] = Purify::config($config)->clean($data[$locale]['options']['css']);
        }

        if (in_array($data['type'], ['image_carousel', 'services_content'])) {
            unset($data[$locale]['options']);
        }

        $theme = parent::update($data, $id);

        if (in_array($data['type'], ['image_carousel', 'services_content'])) {
            $this->uploadImage(request()->all(), $theme);
        }

        return $theme;
    }

    /**
     * Mass update the status of themes in the repository.
     *
     * This method updates multiple records in the database based on the provided
     * theme IDs.
     *
     * @param  int  $themeIds
     * @return int The number of records updated.
     */
    public function massUpdateStatus(array $data, array $themeIds)
    {
        return $this->model->whereIn('id', $themeIds)->update($data);
    }

    /**
     * Upload images
     *
     * @return void|string
     */
    public function uploadImage(array $data, ThemeCustomization $theme)
    {
        $locale = core()->getRequestedLocaleCode();

        if (isset($data[$locale]['deleted_sliders'])) {
            foreach ($data[$locale]['deleted_sliders'] as $slider) {
                Storage::disk(config('filesystems.default'))->delete(str_replace('storage/', '', $slider['image']));
            }
        }

        if (! isset($data[$locale]['options'])) {
            return;
        }

        $options = [];

        foreach ($data[$locale]['options'] as $image) {
            if (isset($image['service_icon'])) {
                $options['services'][] = [
                    'service_icon' => $image['service_icon'],
                    'description' => $image['description'],
                    'title' => $image['title'],
                ];
            } elseif ($image['image'] instanceof UploadedFile) {
                $file = $image['image'];
                $extension = strtolower($file->getClientOriginalExtension());
                $isVideo = in_array($extension, ['mp4', 'webm', 'mov', 'avi']);
                $mediaType = $image['type'] ?? ($isVideo ? 'video' : 'image');

                try {
                    $path = cloudinary_upload($file, 'theme', (string) $theme->id, 'sliders', ! $isVideo);
                } catch (\Exception $e) {
                    session()->flash('error', $e->getMessage());

                    return redirect()->back();
                }

                if (($data['type'] ?? '') == 'static_content') {
                    return Storage::disk(config('filesystems.default'))->url($path);
                }

                $options['images'][] = [
                    'image' => 'storage/'.$path,
                    'link' => $image['link'],
                    'title' => $image['title'],
                    'type' => $mediaType,
                ];
            } else {
                // Preserve existing items with their type field
                if (! isset($image['type'])) {
                    $image['type'] = 'image';
                }

                $options['images'][] = $image;
            }
        }

        $translatedModel = $theme->translate($locale);
        $translatedModel->options = $options ?? [];
        $translatedModel->theme_customization_id = $theme->id;
        $translatedModel->save();
    }
}
