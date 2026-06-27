<?php

namespace Frooxi\Product\Repositories;

use Frooxi\Core\Eloquent\Repository;
use Frooxi\Core\Traits\Sanitizer;
use Frooxi\Product\Contracts\ProductReview;
use Frooxi\Product\Contracts\ProductReviewAttachment;

class ProductReviewAttachmentRepository extends Repository
{
    use Sanitizer;

    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return ProductReviewAttachment::class;
    }

    /**
     * Upload.
     */
    public function upload(array $attachments, ProductReview $review): void
    {
        foreach ($attachments as $attachment) {
            $mimeType = $attachment->getMimeType();

            $fileType = explode('/', $mimeType);

            $path = cloudinary_upload($attachment, 'reviews', (string) $review->id, null, false);

            $this->sanitizeSVG($path, $mimeType);

            $this->create([
                'path' => $path,
                'review_id' => $review->id,
                'type' => $fileType[0],
                'mime_type' => $fileType[1] ?? null,
            ]);
        }
    }
}
