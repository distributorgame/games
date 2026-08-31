<?php

namespace App\Actions\Cms\Web\Slider;

use App\Enums\CacheGroupEnum;
use App\Models\Web\Slider;
use App\Traits\WithMediaCollection;
use App\Traits\WithVersionedCache;
use Illuminate\Http\UploadedFile;

class StoreSliderAction
{
    use WithMediaCollection, WithVersionedCache;

    /**
     * Handle the action.
     */
    public function handle(array $data): Slider
    {
        $slider = Slider::create($data);

        if ($data['image'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $slider,
                file: $data['image'],
                collection: 'image',
            );
        }

        $this->flushCacheGroup(CacheGroupEnum::SLIDERS);

        return $slider;
    }
}
