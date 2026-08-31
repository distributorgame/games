<?php

namespace App\Actions\Cms\PPOB\PPOBCategory;

use App\Enums\CacheGroupEnum;
use App\Models\PPOB\PPOBCategory;
use App\Traits\WithMediaCollection;
use App\Traits\WithVersionedCache;
use Illuminate\Http\UploadedFile;

class UpdatePPOBCategoryAction
{
    use WithMediaCollection, WithVersionedCache;

    /**
     * Handle the action.
     */
    public function handle(PPOBCategory $category, array $data): bool
    {
        if ($data['image'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $category,
                file: $data['image'],
                collection: 'image',
            );
        }

        $category->update($data);

        // Update the status of the brands and products that belong to this category
        $category->brands()->update(['status' => $category->status]);

        $this->flushCacheGroup(CacheGroupEnum::CATEGORIES, CacheGroupEnum::BRANDS);

        return true;
    }
}
