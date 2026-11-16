<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Collection;
use App\Models\GalleryItem;
use App\Models\HomeSection;
use App\Models\Media;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_and_admin_role(): void
    {
        $admin = User::create([
            'name' => 'Admin Tester',
            'email' => 'testadmin@sultanfabrics.com',
            'password' => 'secret123',
            'role' => 'admin',
        ]);

        $this->assertTrue($admin->isAdmin());
    }

    public function test_category_hierarchy_and_bilingual_accessors(): void
    {
        $parent = Category::create([
            'name_ar' => 'أقمشة فاخرة',
            'name_en' => 'Luxury Fabrics',
            'slug_ar' => 'luxury-fabrics-ar',
            'slug_en' => 'luxury-fabrics',
            'is_active' => true,
        ]);

        $child = Category::create([
            'parent_id' => $parent->id,
            'name_ar' => 'أقمشة كشمير',
            'name_en' => 'Cashmere Fabrics',
            'slug_ar' => 'cashmere-fabrics-ar',
            'slug_en' => 'cashmere-fabrics',
            'is_active' => true,
        ]);

        app()->setLocale('ar');
        $this->assertEquals('أقمشة فاخرة', $parent->name);
        $this->assertEquals('luxury-fabrics-ar', $parent->slug);

        app()->setLocale('en');
        $this->assertEquals('Luxury Fabrics', $parent->name);
        $this->assertEquals('luxury-fabrics', $parent->slug);

        $this->assertEquals($parent->id, $child->parent->id);
        $this->assertTrue($parent->children->contains($child));
    }

    public function test_product_category_and_collection_relationships(): void
    {
        $category = Category::create([
            'name_ar' => 'تصنيف تجريبي',
            'name_en' => 'Test Cat',
            'slug_ar' => 'test-cat-ar',
            'slug_en' => 'test-cat',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name_ar' => 'قماش صوف تجريبي',
            'name_en' => 'Test Wool Product',
            'slug_ar' => 'test-wool-product-ar',
            'slug_en' => 'test-wool-product',
            'sku' => 'TEST-SKU-001',
            'price' => 599.50,
            'status' => 'published',
            'is_featured' => true,
        ]);

        $collection = Collection::create([
            'name_ar' => 'مجموعة شتوية',
            'name_en' => 'Winter Selection',
            'slug_ar' => 'winter-selection-ar',
            'slug_en' => 'winter-selection',
            'status' => 'published',
        ]);

        $product->collections()->attach($collection->id, ['sort_order' => 1]);

        $this->assertEquals($category->id, $product->category->id);
        $this->assertTrue($product->collections->contains($collection));
        $this->assertTrue($collection->products->contains($product));
        $this->assertEquals('599.50', $product->price);
    }

    public function test_polymorphic_media_management(): void
    {
        $category = Category::create([
            'name_ar' => 'تصنيف ميديا',
            'name_en' => 'Media Cat',
            'slug_ar' => 'media-cat-ar',
            'slug_en' => 'media-cat',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name_ar' => 'قماش تجريبي',
            'name_en' => 'Test Product',
            'slug_ar' => 'test-prod-ar',
            'slug_en' => 'test-prod',
            'price' => 100.00,
            'status' => 'published',
        ]);

        $media = $product->media()->create([
            'source' => 'external',
            'external_url' => 'https://example.com/test-fabric.jpg',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $this->assertInstanceOf(Media::class, $media);
        $this->assertEquals('https://example.com/test-fabric.jpg', $media->url);
        $this->assertEquals($product->id, $media->mediable->id);
    }

    public function test_key_value_bilingual_settings(): void
    {
        Setting::set('test_tagline', ['ar' => 'شعار تجريبي', 'en' => 'Test Tagline'], 'general', 'json');

        $this->assertEquals('شعار تجريبي', Setting::get('test_tagline', null, 'ar'));
        $this->assertEquals('Test Tagline', Setting::get('test_tagline', null, 'en'));
    }
}
