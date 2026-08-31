<?php

namespace App\Http\Controllers\Main;

use App\Enums\CacheGroupEnum;
use App\Http\Controllers\Controller;
use App\Models\PPOB\PPOBBrand;
use App\Models\PPOB\PPOBCategory;
use App\Models\Web\Slider;
use App\Traits\WithVersionedCache;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use WithVersionedCache;

    /**
     * Highest page number kept in cache, so a crafted `?page=` cannot grow the
     * keyspace without bound. Anything past this is served straight from the database.
     */
    private const MAX_CACHED_PAGE = 5;

    /**
     * Display the main home page.
     */
    public function index(Request $request)
    {
        $settingTitle = getSetting('title');
        $settingFavicon = getSetting('favicon') ?: '/favicon.svg';

        $sliders = $this->flexibleVersioned(CacheGroupEnum::SLIDERS, 'home', [3600, 7200], function () {
            return Slider::query()
                ->with('media')
                ->where('status', true)
                ->orderBy('order')
                ->get()
                ->map(function ($slider) {
                    $slider->image = $slider->getFirstMediaUrl('image');
                    $slider->makeHidden('media');

                    return $slider;
                });
        });

        $categories = $this->flexibleVersioned(CacheGroupEnum::CATEGORIES, 'home', [1800, 3600], function () {
            return PPOBCategory::query()
                ->withCount('activeBrands', 'media')
                ->where('status', true)
                ->get()
                ->map(function ($category) {
                    $category->image = $category->getFirstMediaUrl('image');
                    $category->makeHidden('media');

                    return $category;
                });
        });

        $featuredBrands = $this->flexibleVersioned(CacheGroupEnum::BRANDS, 'home:featured', [1800, 3600], function () {
            return PPOBBrand::query()
                ->with('category', 'media')
                ->where('featured', true)
                ->where('status', true)
                ->orderBy('order')
                ->limit(6)
                ->get()
                ->map(function ($brand) {
                    $brand->image = $brand->getFirstMediaUrl('image');
                    $brand->makeHidden('media');

                    return $brand;
                });
        });

        // Filter category
        $category = null;
        if ($request->has('category')) {
            $category = PPOBCategory::where('slug', $request->query('category'))->first();
        }

        return inertia()->render('main/Home', [
            'sliders' => $sliders,
            'brands' => inertia()->scroll(fn () => $this->paginateBrands($request, $category)),
            'featured_brands' => $featuredBrands,
            'categories' => $categories,
        ])->withViewData([
            'meta' => [
                'title' => $settingTitle,
                'description' => "Selamat datang di {$settingTitle}, tempat topup game termurah dan terpercaya! Nikmati berbagai penawaran menarik untuk topup game favoritmu dengan harga terbaik. Bergabunglah sekarang dan rasakan pengalaman topup yang mudah, cepat, dan aman hanya di {$settingTitle}!",
                'keywords' => "{$settingTitle}, topup game, topup murah, topup terpercaya, penawaran menarik, harga terbaik, pengalaman topup mudah, cepat, aman",
                'author' => $settingTitle,
                'application_name' => $settingTitle,
                'url' => route('home'),
                'image' => config('app.url').$settingFavicon,
            ],
        ]);
    }

    /**
     * Paginated brands for the infinite scroll section, cached per category and page.
     */
    private function paginateBrands(Request $request, ?PPOBCategory $category)
    {
        $page = max(1, (int) $request->query('page', 1));

        if ($page > self::MAX_CACHED_PAGE) {
            return $this->brandsQuery($category);
        }

        $categoryKey = $category?->slug ?: 'all';

        return $this->flexibleVersioned(
            CacheGroupEnum::BRANDS,
            "home:list:{$categoryKey}:page:{$page}",
            [600, 1200],
            fn () => $this->brandsQuery($category),
        );
    }

    private function brandsQuery(?PPOBCategory $category)
    {
        return PPOBBrand::query()
            ->with('category', 'media')
            ->when($category, fn ($query) => $query->where('p_p_o_b_category_id', $category->id))
            ->where('status', true)
            ->orderBy('order')
            ->simplePaginate(12)
            ->through(function ($brand) {
                $brand->image = $brand->getFirstMediaUrl('image');
                $brand->makeHidden('media');

                return $brand;
            });
    }
}
