<?php

use App\Models\PPOB\PPOBBrand;
use App\Models\PPOB\PPOBCategory;
use App\Models\PPOB\PPOBProduct;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\SettingSeeder;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config(['inertia.ssr.enabled' => false]);

    $this->seed(SettingSeeder::class);

    $category = PPOBCategory::create([
        'name' => 'Games',
        'description' => 'Game vouchers',
        'status' => true,
    ]);

    $this->brand = PPOBBrand::create([
        'p_p_o_b_category_id' => $category->id,
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
});

it('never serializes the cost price by default', function () {
    expect($this->product->toArray())->not->toHaveKey('buy_price')
        ->and($this->product->toArray())->toHaveKey('sell_price')
        ->and($this->product->buy_price)->toBe(20000);
});

it('hides the cost price on the brand detail page', function () {
    $this->get(route('product.show', $this->brand->slug))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('brand.products.0')
            ->where('brand.products.0.sell_price', 22000)
            ->missing('brand.products.0.buy_price')
        );
});

it('still exposes the cost price to the admin panel', function () {
    $this->seed(PermissionSeeder::class);

    $superadmin = User::factory()->create();
    $superadmin->assignRole('superadmin');

    $this->actingAs($superadmin)
        ->get(route('cms.ppob.products.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('data.data.0.buy_price', 20000)
        );

    $this->actingAs($superadmin)
        ->get(route('cms.ppob.products.edit', $this->product->slug))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('product.buy_price', 20000)
        );
});
