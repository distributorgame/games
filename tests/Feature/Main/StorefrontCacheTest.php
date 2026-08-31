<?php

use App\Actions\Cms\PPOB\PPOBProduct\UpdatePPOBProductAction;
use App\Actions\Cms\Web\Slider\StoreSliderAction;
use App\Models\PPOB\PPOBBrand;
use App\Models\PPOB\PPOBCategory;
use App\Models\PPOB\PPOBProduct;
use App\Models\Web\Slider;
use Database\Seeders\SettingSeeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config(['inertia.ssr.enabled' => false]);

    $this->seed(SettingSeeder::class);

    $this->category = PPOBCategory::create([
        'name' => 'Games',
        'description' => 'Game vouchers',
        'status' => true,
    ]);

    $this->brand = PPOBBrand::create([
        'p_p_o_b_category_id' => $this->category->id,
        'name' => 'Mobile Legends',
        'provider' => 'digiflazz',
        'description' => 'MLBB diamonds',
        'featured' => true,
        'order' => 1,
        'status' => true,
    ]);

    $this->product = PPOBProduct::create([
        'p_p_o_b_brand_id' => $this->brand->id,
        'name' => '86 Diamonds',
        'sku' => 'ML86',
        'provider' => 'digiflazz',
        'buy_price' => 20000,
        'sell_price' => 22000,
        'status' => true,
    ]);

    Slider::create(['title' => 'Promo', 'order' => 1, 'status' => true]);
});

function queriesFor(Closure $callback): int
{
    DB::flushQueryLog();
    DB::enableQueryLog();

    $callback();

    $count = count(DB::getQueryLog());
    DB::disableQueryLog();

    return $count;
}

it('serves the home page from cache on the second visit', function () {
    $cold = queriesFor(fn () => $this->get(route('home'))->assertOk());
    $warm = queriesFor(fn () => $this->get(route('home'))->assertOk());

    expect($warm)->toBeLessThan($cold);
});

it('keeps brands as an infinite scroll prop', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('main/Home')
            ->has('brands.data', 1)
        );

    $this->get(route('home'), [
        'X-Inertia' => 'true',
        'X-Inertia-Version' => inertia()->getVersion(),
        'X-Inertia-Partial-Component' => 'main/Home',
        'X-Inertia-Partial-Data' => 'brands',
    ])->assertOk()->assertJsonStructure([
        'scrollProps' => [
            'brands' => ['pageName', 'previousPage', 'nextPage', 'currentPage'],
        ],
    ]);
});

it('caches only the first few pages of the brand list', function () {
    $this->get(route('home', ['page' => 1]))->assertOk();
    $this->get(route('home', ['page' => 99999]))->assertOk();

    expect(Cache::get('brands:home:list:all:page:1:v1'))->not->toBeNull()
        ->and(Cache::get('brands:home:list:all:page:99999:v1'))->toBeNull();
});

it('survives a page parameter that is not an integer', function () {
    $this->get(route('home').'?page[]=1')->assertOk();
    $this->get(route('home', ['page' => -5]))->assertOk();
});

it('serves the brand detail page from cache on the second visit', function () {
    $cold = queriesFor(fn () => $this->get(route('product.show', $this->brand->slug))->assertOk());
    $warm = queriesFor(fn () => $this->get(route('product.show', $this->brand->slug))->assertOk());

    expect($warm)->toBeLessThan($cold);
});

it('invalidates the brand detail cache when a product price changes', function () {
    $this->get(route('product.show', $this->brand->slug))
        ->assertInertia(fn (Assert $page) => $page->where('brand.products.0.sell_price', 22000));

    app(UpdatePPOBProductAction::class)->handle($this->product, [
        'name' => $this->product->name,
        'buy_price' => '25000',
        'sell_price' => '27500',
    ]);

    $this->get(route('product.show', $this->brand->slug))
        ->assertInertia(fn (Assert $page) => $page->where('brand.products.0.sell_price', 27500));
});

it('invalidates the slider cache when a slider is added', function () {
    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page->has('sliders', 1));

    app(StoreSliderAction::class)->handle([
        'title' => 'Second promo',
        'order' => 2,
        'status' => true,
    ]);

    $this->get(route('home'))
        ->assertInertia(fn (Assert $page) => $page->has('sliders', 2));
});
