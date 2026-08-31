<?php

namespace App\Actions\Cms\Web\Slider;

use App\Enums\CacheGroupEnum;
use App\Models\Web\Slider;
use App\Traits\WithVersionedCache;

class DeleteSliderAction
{
    use WithVersionedCache;

    /**
     * Handle the action.
     */
    public function handle(Slider $slider): ?bool
    {
        $result = $slider->delete();

        $this->flushCacheGroup(CacheGroupEnum::SLIDERS);

        return $result;
    }
}
