<?php

namespace Tests\Feature;

use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Categories\CategoryIndex;
use App\Livewire\Admin\Dashboard\Overview;
use App\Livewire\Admin\Gallery\GalleryIndex;
use App\Livewire\Admin\Pages\PageIndex;
use App\Livewire\Admin\Products\ProductIndex;
use App\Livewire\Admin\Settings\SettingsManager;
use App\Livewire\Admin\Sliders\SliderIndex;
use App\Models\Category;
use App\Models\GalleryItem;
use App\Models\Page;
use App\Models\Setting;
use App\Models\Slider;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Sultan Administrator',
            'email' => 'admin@sultanfabrics.com',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/admin/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/admin/login');
        $response->assertStatus(200);
        $response->assertSeeLivewire(Login::class);
    }

    public function test_admin_can_authenticate_via_livewire(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@sultanfabrics.com')
            ->set('password', 'password123')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_authenticated_admin_can_access_dashboard_and_modules(): void
    {
        $this->actingAs($this->admin);

        $routes = [
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

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }

    public function test_category_crud_via_livewire(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CategoryIndex::class)
            ->call('openCreateModal')
            ->assertSet('showModal', true)
            ->set('form.name_en', 'Silk Velvets')
            ->set('form.name_ar', 'مخمل حريري')
            ->set('form.slug_en', 'silk-velvets')
            ->set('form.slug_ar', 'silk-velvets-ar')
            ->set('form.image_source', 'external')
            ->set('form.image_url', 'https://example.com/velvet.jpg')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('categories', [
            'slug_en' => 'silk-velvets',
            'name_ar' => 'مخمل حريري',
        ]);

        $category = Category::where('slug_en', 'silk-velvets')->first();

        // Test toggle active
        Livewire::test(CategoryIndex::class)
            ->call('toggleActive', $category->id);

        $this->assertFalse($category->fresh()->is_active);
    }

    public function test_product_crud_via_livewire(): void
    {
        $this->actingAs($this->admin);

        $cat = Category::create([
            'name_ar' => 'أقمشة كشمير',
            'name_en' => 'Cashmere',
            'slug_ar' => 'cashmere-ar',
            'slug_en' => 'cashmere',
        ]);

        Livewire::test(ProductIndex::class)
            ->call('openCreateModal')
            ->set('form.category_id', $cat->id)
            ->set('form.name_en', 'Pure Mongolian Cashmere')
            ->set('form.name_ar', 'كشمير منغولي نقي')
            ->set('form.slug_en', 'pure-mongolian-cashmere')
            ->set('form.slug_ar', 'pure-mongolian-cashmere-ar')
            ->set('form.sku', 'CSH-MNG-001')
            ->set('form.price', 3500.00)
            ->set('form.status', 'published')
            ->set('form.image_source', 'external')
            ->set('form.image_url', 'https://example.com/cashmere.jpg')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('products', [
            'sku' => 'CSH-MNG-001',
            'status' => 'published',
        ]);
    }

    public function test_settings_manager_saves_via_livewire(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(SettingsManager::class)
            ->set('site_name_en', 'Sultan Textiles')
            ->set('site_name_ar', 'سلطان المنسوجات')
            ->set('whatsapp_number', '201234567890')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals('Sultan Textiles', Setting::get('site_name', null, 'en'));
        $this->assertEquals('سلطان المنسوجات', Setting::get('site_name', null, 'ar'));
        $this->assertEquals('201234567890', Setting::get('whatsapp_number'));
    }

    public function test_slider_crud_via_livewire(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(SliderIndex::class)
            ->call('openCreateModal')
            ->set('form.title_en', 'Luxury Winter Silk Collection')
            ->set('form.title_ar', 'تشكيلة الحرير الشتوي الفاخر')
            ->set('form.subtitle_en', 'Finest Egyptian Weaves')
            ->set('form.subtitle_ar', 'أرقى المنسوجات المصرية')
            ->set('form.button_text_en', 'Explore Now')
            ->set('form.button_text_ar', 'استكشف الآن')
            ->set('form.button_url', '/collections/winter')
            ->set('form.is_active', true)
            ->set('form.image_source', 'external')
            ->set('form.image_url', 'https://example.com/slider.jpg')
            ->call('save')
            ->assertHasNoErrors();

        $slider = Slider::where('title_en', 'Luxury Winter Silk Collection')->first();
        $this->assertNotNull($slider);
        $this->assertEquals('تشكيلة الحرير الشتوي الفاخر', $slider->title_ar);
        $this->assertEquals('https://example.com/slider.jpg', $slider->primaryImage?->external_url);

        // Edit
        Livewire::test(SliderIndex::class)
            ->call('openEditModal', $slider->id)
            ->set('form.title_en', 'Updated Winter Silk Collection')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals('Updated Winter Silk Collection', $slider->fresh()->title_en);
    }

    public function test_gallery_crud_via_livewire(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(GalleryIndex::class)
            ->call('openCreateModal')
            ->set('form.title_en', 'Grand Atelier Display')
            ->set('form.title_ar', 'معرض المشغل الرئيسي')
            ->set('form.description_en', 'Showcasing authentic Egyptian embroidery.')
            ->set('form.description_ar', 'استعراض التطريز المصري الأصيل.')
            ->set('form.image_source', 'external')
            ->set('form.image_url', 'https://example.com/gallery.jpg')
            ->call('save')
            ->assertHasNoErrors();

        $item = GalleryItem::where('title_en', 'Grand Atelier Display')->first();
        $this->assertNotNull($item);
        $this->assertEquals('معرض المشغل الرئيسي', $item->title_ar);

        // Toggle active
        Livewire::test(GalleryIndex::class)
            ->call('toggleActive', $item->id);

        $this->assertFalse($item->fresh()->is_active);
    }

    public function test_page_crud_via_livewire(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(PageIndex::class)
            ->call('openCreateModal')
            ->set('form.title_en', 'About Sultan Heritage')
            ->set('form.title_ar', 'عن تراث السلطان')
            ->set('form.slug_en', 'about-sultan-heritage')
            ->set('form.slug_ar', 'about-sultan-heritage-ar')
            ->set('form.content_en', 'Three decades of mastery in premium textiles.')
            ->set('form.content_ar', 'ثلاثة عقود من الريادة في أرقى الأقمشة.')
            ->set('form.status', 'published')
            ->call('save')
            ->assertHasNoErrors();

        $page = Page::where('slug_en', 'about-sultan-heritage')->first();
        $this->assertNotNull($page);
        $this->assertEquals('عن تراث السلطان', $page->title_ar);

        // Update with same slug to ensure ignore($page->id) works
        Livewire::test(PageIndex::class)
            ->call('openEditModal', $page->id)
            ->set('form.title_en', 'About The Sultan Heritage - Updated')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertEquals('About The Sultan Heritage - Updated', $page->fresh()->title_en);
    }
}
