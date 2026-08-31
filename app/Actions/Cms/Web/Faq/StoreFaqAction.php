<?php

namespace App\Actions\Cms\Web\Faq;

use App\Enums\CacheGroupEnum;
use App\Models\Web\Faq;
use App\Traits\WithVersionedCache;

class StoreFaqAction
{
    use WithVersionedCache;

    /**
     * Handle the action.
     */
    public function handle(array $data): Faq
    {
        $faq = Faq::create($data);

        $this->flushCacheGroup(CacheGroupEnum::FAQS);

        return $faq;
    }
}
