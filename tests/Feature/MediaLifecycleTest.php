<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Collection;
use App\Models\GalleryItem;
use App\Models\Media;
use App\Models\Product;
use App\Models\Slider;
use App\Services\MediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected MediaService $mediaService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mediaService = app(MediaService::class);
        MediaService::resetDeletedPaths();
    }

    /**
     * Test 1: حذف صورة واحدة
     * Testing both MediaService::deleteMedia() and direct $media->delete().
     */
    public function test_delete_single_media_removes_physical_file_from_storage(): void
    {
        Storage::fake('public');

        $category = Category::create([
            'name_ar' => 'أقمشة فاخرة',
            'name_en' => 'Luxury Fabrics',
            'slug_ar' => 'luxury-fabrics-ar',
            'slug_en' => 'luxury-fabrics',
        ]);

        $file = UploadedFile::fake()->image('single-image.jpg', 600, 600);
        $media = $this->mediaService->attachLocal($category, $file, isPrimary: true, directory: 'categories');

        Storage::disk('public')->assertExists($media->path);
        $this->assertDatabaseHas('media', ['id' => $media->id]);

        $deleted = $this->mediaService->deleteMedia($media);

        $this->assertTrue($deleted);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    /**
     * Test 1b: Direct $media->delete() removes physical file from storage.
     */
    public function test_direct_media_model_delete_removes_physical_file(): void
    {
        Storage::fake('public');

        $slider = Slider::create([
            'title_ar' => 'شريحة رئيسية',
            'title_en' => 'Main Hero',
        ]);

        $file = UploadedFile::fake()->image('direct-delete.png');
        $media = $this->mediaService->attachLocal($slider, $file, isPrimary: true, directory: 'sliders');

        Storage::disk('public')->assertExists($media->path);

        $media->delete();

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    /**
     * Test 2: حذف جميع صور المنتج (الأساسية والإضافية)
     * Testing parent Product deletion cleans up all local media files.
     */
    public function test_delete_product_removes_all_its_primary_and_additional_images(): void
    {
        Storage::fake('public');

        $category = Category::create([
            'name_ar' => 'حرير',
            'name_en' => 'Silk',
            'slug_ar' => 'silk-ar',
            'slug_en' => 'silk',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name_ar' => 'حرير إيطالي طبيعي',
            'name_en' => 'Italian Pure Silk',
            'slug_ar' => 'italian-pure-silk-ar',
            'slug_en' => 'italian-pure-silk',
            'price' => 750.00,
            'status' => 'published',
        ]);

        $primaryFile = UploadedFile::fake()->image('silk-primary.jpg');
        $galleryFile1 = UploadedFile::fake()->image('silk-detail-1.jpg');
        $galleryFile2 = UploadedFile::fake()->image('silk-detail-2.jpg');

        $primaryMedia = $this->mediaService->attachLocal($product, $primaryFile, isPrimary: true, directory: 'products');
        $galleryMedia1 = $this->mediaService->attachLocal($product, $galleryFile1, isPrimary: false, directory: 'products');
        $galleryMedia2 = $this->mediaService->attachLocal($product, $galleryFile2, isPrimary: false, directory: 'products');

        Storage::disk('public')->assertExists($primaryMedia->path);
        Storage::disk('public')->assertExists($galleryMedia1->path);
        Storage::disk('public')->assertExists($galleryMedia2->path);

        $this->assertEquals(3, $product->media()->count());

        $product->delete();

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        $this->assertDatabaseMissing('media', ['id' => $primaryMedia->id]);
        $this->assertDatabaseMissing('media', ['id' => $galleryMedia1->id]);
        $this->assertDatabaseMissing('media', ['id' => $galleryMedia2->id]);

        Storage::disk('public')->assertMissing($primaryMedia->path);
        Storage::disk('public')->assertMissing($galleryMedia1->path);
        Storage::disk('public')->assertMissing($galleryMedia2->path);
    }

    /**
     * Test 3: حذف تصنيف له صور
     */
    public function test_delete_category_removes_its_media_and_storage_file(): void
    {
        Storage::fake('public');

        $category = Category::create([
            'name_ar' => 'أصواف شتوية',
            'name_en' => 'Winter Wools',
            'slug_ar' => 'winter-wools-ar',
            'slug_en' => 'winter-wools',
        ]);

        $file = UploadedFile::fake()->image('category-cover.webp');
        $media = $this->mediaService->attachLocal($category, $file, isPrimary: true, directory: 'categories');

        Storage::disk('public')->assertExists($media->path);

        $category->delete();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    /**
     * Test 4: حذف Slider له صورة
     */
    public function test_delete_slider_removes_its_media_and_storage_file(): void
    {
        Storage::fake('public');

        $slider = Slider::create([
            'title_ar' => 'عروض الموسم',
            'title_en' => 'Season Offers',
        ]);

        $file = UploadedFile::fake()->image('slider-banner.jpg');
        $media = $this->mediaService->attachLocal($slider, $file, isPrimary: true, directory: 'sliders');

        Storage::disk('public')->assertExists($media->path);

        $slider->delete();

        $this->assertDatabaseMissing('sliders', ['id' => $slider->id]);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    /**
     * Test 5: حذف Gallery Item
     */
    public function test_delete_gallery_item_removes_its_media_and_storage_file(): void
    {
        Storage::fake('public');

        $item = GalleryItem::create([
            'title_ar' => 'معرض السلطان - جناح 1',
            'title_en' => 'Sultan Showroom - Hall 1',
        ]);

        $file = UploadedFile::fake()->image('gallery-hall.jpg');
        $media = $this->mediaService->attachLocal($item, $file, isPrimary: true, directory: 'gallery');

        Storage::disk('public')->assertExists($media->path);

        $item->delete();

        $this->assertDatabaseMissing('gallery_items', ['id' => $item->id]);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    /**
     * Test 5b: حذف Collection له صور
     */
    public function test_delete_collection_removes_its_media_and_storage_file(): void
    {
        Storage::fake('public');

        $collection = Collection::create([
            'name_ar' => 'تشكيلة رمضان',
            'name_en' => 'Ramadan Collection',
            'slug_ar' => 'ramadan-collection-ar',
            'slug_en' => 'ramadan-collection',
            'status' => 'published',
        ]);

        $file = UploadedFile::fake()->image('collection-cover.jpg');
        $media = $this->mediaService->attachLocal($collection, $file, isPrimary: true, directory: 'collections');

        Storage::disk('public')->assertExists($media->path);

        $collection->delete();

        $this->assertDatabaseMissing('collections', ['id' => $collection->id]);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    /**
     * Test 6: التأكد من عدم حذف External URL
     */
    public function test_external_url_media_deletion_does_not_call_storage_delete(): void
    {
        Storage::fake('public');

        $slider = Slider::create([
            'title_ar' => 'شريحة برابط خارجي',
            'title_en' => 'External Slider',
        ]);

        $externalUrl = 'https://cdn.the-sultan.com/banners/hero-external.webp';
        $media = $this->mediaService->attachExternal($slider, $externalUrl, isPrimary: true);

        $this->assertEquals('external', $media->source);
        $this->assertEquals($externalUrl, $media->external_url);
        $this->assertNull($media->path);

        // Deleting the external media record
        $slider->delete();

        $this->assertDatabaseMissing('sliders', ['id' => $slider->id]);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);

        // Verify Storage public has zero files deleted or touched
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    /**
     * Test 7: التأكد من عدم حذف ملفات غير مرتبطة بالعنصر
     */
    public function test_unrelated_files_in_storage_are_preserved(): void
    {
        Storage::fake('public');

        // Create unrelated files in storage
        Storage::disk('public')->put('system/logo.png', 'logo-content');
        Storage::disk('public')->put('products/other-product-image.jpg', 'other-content');
        Storage::disk('public')->put('documents/catalog.pdf', 'pdf-content');

        $category = Category::create([
            'name_ar' => 'قطن مصري',
            'name_en' => 'Egyptian Cotton',
            'slug_ar' => 'egyptian-cotton-ar',
            'slug_en' => 'egyptian-cotton',
        ]);

        $file = UploadedFile::fake()->image('category-file.jpg');
        $media = $this->mediaService->attachLocal($category, $file, isPrimary: true, directory: 'categories');

        // Delete the category
        $category->delete();

        // The category media file is deleted
        Storage::disk('public')->assertMissing($media->path);

        // All unrelated files MUST still exist
        Storage::disk('public')->assertExists('system/logo.png');
        Storage::disk('public')->assertExists('products/other-product-image.jpg');
        Storage::disk('public')->assertExists('documents/catalog.pdf');
    }

    /**
     * Test 8: مراعاة Transactions - التراجع (Rollback) يمنع حذف الملف من Storage
     */
    public function test_database_transaction_rollback_prevents_storage_file_deletion(): void
    {
        Storage::fake('public');

        $category = Category::create([
            'name_ar' => 'كتان بلجيكي',
            'name_en' => 'Belgian Linen',
            'slug_ar' => 'belgian-linen-ar',
            'slug_en' => 'belgian-linen',
        ]);

        $file = UploadedFile::fake()->image('linen.jpg');
        $media = $this->mediaService->attachLocal($category, $file, isPrimary: true, directory: 'categories');

        Storage::disk('public')->assertExists($media->path);

        try {
            DB::transaction(function () use ($category) {
                $category->delete();
                // Simulate an exception that causes a transaction rollback
                throw new \RuntimeException('Simulated failure during transaction');
            });
        } catch (\RuntimeException $e) {
            // Expected exception caught
        }

        // The DB record must still exist because of rollback
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
        $this->assertDatabaseHas('media', ['id' => $media->id]);

        // The storage file MUST still exist! Rollback protected it.
        Storage::disk('public')->assertExists($media->path);
    }

    /**
     * Test 9: مراعاة Transactions - إتمام العملية (Commit) يحذف الملف من Storage
     */
    public function test_database_transaction_commit_executes_storage_file_deletion(): void
    {
        Storage::fake('public');

        $category = Category::create([
            'name_ar' => 'مخمل ملكي',
            'name_en' => 'Royal Velvet',
            'slug_ar' => 'royal-velvet-ar',
            'slug_en' => 'royal-velvet',
        ]);

        $file = UploadedFile::fake()->image('velvet.jpg');
        $media = $this->mediaService->attachLocal($category, $file, isPrimary: true, directory: 'categories');

        Storage::disk('public')->assertExists($media->path);

        DB::transaction(function () use ($category) {
            $category->delete();
        });

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($media->path);
    }

    /**
     * Test 10: حماية الملفات المشتركة - عدم حذف ملف إذا كان مرتبطاً بسجل Media آخر
     */
    public function test_shared_file_is_not_deleted_if_still_referenced_by_another_media_record(): void
    {
        Storage::fake('public');

        $cat1 = Category::create(['name_ar' => 'تصنيف 1', 'name_en' => 'Cat 1', 'slug_ar' => 'cat-1-ar', 'slug_en' => 'cat-1']);
        $cat2 = Category::create(['name_ar' => 'تصنيف 2', 'name_en' => 'Cat 2', 'slug_ar' => 'cat-2-ar', 'slug_en' => 'cat-2']);

        $file = UploadedFile::fake()->image('shared-texture.jpg');
        $media1 = $this->mediaService->attachLocal($cat1, $file, isPrimary: true, directory: 'shared');

        // Create media2 pointing to the exact same disk and path
        $media2 = $cat2->media()->create([
            'source' => 'local',
            'disk' => 'public',
            'path' => $media1->path,
            'is_primary' => true,
        ]);

        Storage::disk('public')->assertExists($media1->path);

        // Delete first category
        $cat1->delete();

        // media1 is deleted from DB
        $this->assertDatabaseMissing('media', ['id' => $media1->id]);
        // media2 is still in DB
        $this->assertDatabaseHas('media', ['id' => $media2->id]);

        // The physical file MUST NOT be deleted because media2 still uses it!
        Storage::disk('public')->assertExists($media1->path);

        // Now delete second category
        $cat2->delete();

        $this->assertDatabaseMissing('media', ['id' => $media2->id]);
        // Now that no record references it, the file should be deleted
        Storage::disk('public')->assertMissing($media1->path);
    }

    /**
     * Test 11: منع تكرار عمليات الحذف والتعامل مع الملفات غير الموجودة دون أخطاء
     */
    public function test_idempotent_deletion_and_missing_file_safety(): void
    {
        Storage::fake('public');

        $slider = Slider::create([
            'title_ar' => 'شريحة مفقودة',
            'title_en' => 'Missing File Slider',
        ]);

        $media = $slider->media()->create([
            'source' => 'local',
            'disk' => 'public',
            'path' => 'sliders/non-existent-file-on-disk.jpg',
            'is_primary' => true,
        ]);

        // Ensure file does not actually exist on disk
        Storage::disk('public')->assertMissing($media->path);

        // Should execute smoothly without throwing exceptions
        $this->mediaService->deleteMedia($media);

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }
}
