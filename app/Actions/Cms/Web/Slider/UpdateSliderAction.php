<?php

namespace App\Actions\Cms\Web\Slider;

use App\Enums\CacheGroupEnum;
use App\Models\Web\Slider;
use App\Traits\WithMediaCollection;
use App\Traits\WithVersionedCache;
use Illuminate\Http\UploadedFile;

class UpdateSliderAction
{
    use WithMediaCollection, WithVersionedCache;

    /**
     * Handle the action.
     */
    public function handle(Slider $slider, array $data): bool
    {
        if ($data['image'] ?? null instanceof UploadedFile) {
            $this->saveMedia(
                model: $slider,
                file: $data['image'],
                collection: 'image',
            );
        }

        $result = $slider->update($data);

        $this->flushCacheGroup(CacheGroupEnum::SLIDERS);

        return $result;
    }
}
