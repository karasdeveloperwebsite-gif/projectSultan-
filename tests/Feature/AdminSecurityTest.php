<?php

namespace Tests\Feature;

use App\Livewire\Admin\Categories\CategoryIndex;
use App\Livewire\Admin\Collections\CollectionIndex;
use App\Livewire\Admin\Gallery\GalleryIndex;
use App\Livewire\Admin\Inquiries\ContactMessageIndex;
use App\Livewire\Admin\Pages\PageIndex;
use App\Livewire\Admin\Products\ProductIndex;
use App\Livewire\Admin\Sections\HomeSectionIndex;
use App\Livewire\Admin\Settings\SettingsManager;
use App\Livewire\Admin\Sliders\SliderIndex;
use App\Models\Category;
use App\Models\Collection;
use App\Models\ContactMessage;
use App\Models\GalleryItem;
use App\Models\HomeSection;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create([
            'email' => 'admin@sultanfabrics.com',
        ]);

        $this->customer = User::factory()->create([
            'email' => 'customer@example.com',
            'role' => 'customer',
        ]);
    }

    public function test_guest_is_redirected_to_login_from_all_admin_routes(): void
    {
        $adminRoutes = [
            '/admin',
            '/admin/categories',
            '/admin/products',
            '/admin/collections',
            '/admin/sliders',
            '/admin/gallery',
            '/admin/pages',
            '/admin/inquiries',
            '/admin/settings',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            $response->assertRedirect('/admin/login');
        }
    }

    public function test_authenticated_customer_gets_403_forbidden_on_admin_routes(): void
    {
        $this->actingAs($this->customer);

        $adminRoutes = [
            '/admin',
            '/admin/categories',
            '/admin/products',
            '/admin/collections',
            '/admin/sliders',
            '/admin/gallery',
            '/admin/pages',
            '/admin/inquiries',
            '/admin/settings',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            $response->assertStatus(403);
        }
    }

    public function test_authenticated_admin_can_access_all_admin_routes(): void
    {
        $this->actingAs($this->admin);

        $adminRoutes = [
            '/admin',
            '/admin/categories',
            '/admin/products',
            '/admin/collections',
            '/admin/sliders',
            '/admin/gallery',
            '/admin/pages',
            '/admin/inquiries',
            '/admin/settings',
        ];

        foreach ($adminRoutes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_customer_cannot_call_category_livewire_actions(): void
    {
        $this->actingAs($this->customer);

        $category = Category::create([
            'name_ar' => 'أقمشة',
            'name_en' => 'Fabrics',
            'slug_ar' => 'fabrics-ar',
            'slug_en' => 'fabrics',
            'is_active' => true,
        ]);

        // Rendering should fail with 403 / AuthorizationException
        Livewire::test(CategoryIndex::class)
            ->assertForbidden();
    }

    public function test_customer_cannot_call_product_livewire_actions(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(ProductIndex::class)
            ->assertForbidden();
    }

    public function test_customer_cannot_call_collection_livewire_actions(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(CollectionIndex::class)
            ->assertForbidden();
    }

    public function test_customer_cannot_call_slider_livewire_actions(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(SliderIndex::class)
            ->assertForbidden();
    }

    public function test_customer_cannot_call_gallery_livewire_actions(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(GalleryIndex::class)
            ->assertForbidden();
    }

    public function test_customer_cannot_call_page_livewire_actions(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(PageIndex::class)
            ->assertForbidden();
    }

    public function test_customer_cannot_call_inquiries_livewire_actions(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(ContactMessageIndex::class)
            ->assertForbidden();
    }

    public function test_customer_cannot_call_settings_livewire_actions(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(SettingsManager::class)
            ->assertForbidden();
    }

    public function test_customer_cannot_call_sections_livewire_actions(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(HomeSectionIndex::class)
            ->assertForbidden();
    }

    public function test_admin_can_perform_crud_actions_verified_by_policies(): void
    {
        $this->actingAs($this->admin);

        // 1. Category policy verification
        $category = Category::create([
            'name_ar' => 'حرير',
            'name_en' => 'Silk',
            'slug_ar' => 'silk-ar',
            'slug_en' => 'silk',
            'is_active' => true,
        ]);

        Livewire::test(CategoryIndex::class)
            ->call('toggleActive', $category->id)
            ->assertStatus(200);

        $this->assertFalse($category->fresh()->is_active);

        // 2. Product policy verification
        $product = Product::create([
            'category_id' => $category->id,
            'name_ar' => 'حرير ملكي',
            'name_en' => 'Royal Silk',
            'slug_ar' => 'royal-silk-ar',
            'slug_en' => 'royal-silk',
            'price' => 1500.00,
            'status' => 'published',
        ]);

        Livewire::test(ProductIndex::class)
            ->call('toggleFeatured', $product->id)
            ->assertStatus(200);

        $this->assertTrue($product->fresh()->is_featured);

        // 3. Delete policy verification
        Livewire::test(ProductIndex::class)
            ->call('deleteProduct', $product->id)
            ->assertStatus(200);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
