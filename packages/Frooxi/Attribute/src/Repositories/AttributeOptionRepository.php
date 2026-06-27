<?php

namespace Frooxi\Attribute\Repositories;

use Frooxi\Attribute\Contracts\AttributeOption;
use Frooxi\Core\Eloquent\Repository;
use Illuminate\Http\UploadedFile;

class AttributeOptionRepository extends Repository
{
    /**
     * Specify Model class name
     */
    public function model(): string
    {
        return 'Frooxi\Attribute\Contracts\AttributeOption';
    }

    /**
     * @return AttributeOption
     */
    public function create(array $data)
    {
        $option = parent::create($data);

        $this->uploadSwatchImage($data, $option->id);

        return $option;
    }

    /**
     * @param  int  $id
     * @param  string  $attribute
     * @return AttributeOption
     */
    public function update(array $data, $id)
    {
        $option = parent::update($data, $id);

        $this->uploadSwatchImage($data, $id);

        return $option;
    }

    /**
     * @param  array  $data
     * @param  int  $optionId
     * @return void
     */
    public function uploadSwatchImage($data, $optionId)
    {
        if (empty($data['swatch_value'])) {
            return;
        }

        if ($data['swatch_value'] instanceof UploadedFile) {
            parent::update([
                'swatch_value' => cloudinary_upload($data['swatch_value'], 'attributes', 'swatches'),
            ], $optionId);
        }
    }
}
