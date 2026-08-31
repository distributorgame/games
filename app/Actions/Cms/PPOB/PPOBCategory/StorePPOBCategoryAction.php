<?php

namespace App\Actions\Cms\PPOB\PPOBCategory;

use App\Enums\CacheGroupEnum;
use App\Models\PPOB\PPOBCategory;
use App\Traits\WithMediaCollection;
use App\Traits\WithVersionedCache;
use Illuminate\Http\UploadedFile;

class StorePPOBCategoryAction
{
    use WithMediaCollection, WithVersionedCache;

    /**
     * Handle the action.
     */
    public function handle(array $data): PPOBCategory
    {
        $category = PPOBCategory::create($data);

        if ($data['image'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $category,
                file: $data['image'],
                collection: 'image',
            );
        }

        $this->flushCacheGroup(CacheGroupEnum::CATEGORIES);

        return $category;
    }
}
