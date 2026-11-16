<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with commercial baseline data.
     */
    public function run(): void
    {
        // 1. Administrator Account
        $admin = User::firstOrCreate(
            ['email' => 'admin@sultanfabrics.com'],
            [
                'name' => 'Sultan Administrator',
                'password' => Hash::make(env('ADMIN_INITIAL_PASSWORD', 'Sultan@Admin#2026!Secure')),
                'role' => 'admin',
            ]
        );

        // 2. Global Site Settings
        Setting::set('site_name', ['ar' => 'سلطان الأقمشة', 'en' => 'Sultan Fabrics'], 'general', 'json');
        Setting::set('site_tagline', ['ar' => 'أفخم وأجود أنواع الأقمشة والمنسوجات الرجالية والنسائية', 'en' => 'Finest Fabrics & Tailoring Textiles'], 'general', 'json');
        Setting::set('whatsapp_number', '201000000000', 'contact', 'string');
        Setting::set('contact_phone', '+20 100 000 0000', 'contact', 'string');
        Setting::set('contact_email', 'contact@sultanfabrics.com', 'contact', 'string');
        Setting::set('address', ['ar' => 'جمهورية مصر العربية', 'en' => 'Egypt'], 'contact', 'json');
        Setting::set('currency_code', 'EGP', 'general', 'string');
        Setting::set('instagram_url', 'https://instagram.com/sultanfabrics', 'social', 'string');
        Setting::set('facebook_url', 'https://facebook.com/sultanfabrics', 'social', 'string');
        Setting::set('tiktok_url', 'https://tiktok.com/@sultanfabrics', 'social', 'string');

        // 3. Hierarchical Categories
        $silk = Category::create([
            'name_ar' => 'أقمشة الحرير الطبيعي',
            'name_en' => 'Natural Silk Fabrics',
            'slug_ar' => 'natural-silk-fabrics-ar',
            'slug_en' => 'natural-silk-fabrics',
            'description_ar' => 'تشكيلة مختارة من أرقى خامات الحرير الطبيعي والمغزول بعناية فائقة.',
            'description_en' => 'Carefully curated selection of premium natural silk textiles.',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $silk->media()->create([
            'source' => 'external',
            'external_url' => 'https://images.unsplash.com/photo-1617038220319-276d3cfab638?w=800&auto=format&fit=crop&q=80',
            'alt_text_ar' => 'حرير طبيعي فاخر',
            'alt_text_en' => 'Luxury Natural Silk',
            'is_primary' => true,
        ]);

        $italianSilk = Category::create([
            'parent_id' => $silk->id,
            'name_ar' => 'الحرير الإيطالي',
            'name_en' => 'Italian Silk',
            'slug_ar' => 'italian-silk-ar',
            'slug_en' => 'italian-silk',
            'description_ar' => 'حرير إيطالي كلاسيكي مناسب للمناسبات والتصميم الراقي.',
            'description_en' => 'Classic Italian woven silk suitable for bespoke creations.',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $wool = Category::create([
            'name_ar' => 'أقمشة الصوف والشتوي',
            'name_en' => 'Wool & Winter Fabrics',
            'slug_ar' => 'wool-winter-fabrics-ar',
            'slug_en' => 'wool-winter-fabrics',
            'description_ar' => 'أجود أنواع الصوف الطبيعي والكشمير والمخمل الفاخر.',
            'description_en' => 'Finest natural wool, cashmere, and luxury winter weaves.',
            'is_active' => true,
            'sort_order' => 2,
        ]);
        $wool->media()->create([
            'source' => 'external',
            'external_url' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=800&auto=format&fit=crop&q=80',
            'alt_text_ar' => 'صوف كشمير فاخر',
            'alt_text_en' => 'Luxury Cashmere Wool',
            'is_primary' => true,
        ]);

        $cotton = Category::create([
            'name_ar' => 'أقمشة القطن والكتان',
            'name_en' => 'Cotton & Linen Fabrics',
            'slug_ar' => 'cotton-linen-fabrics-ar',
            'slug_en' => 'cotton-linen-fabrics',
            'description_ar' => 'أقمشة قطنية مصرية ومستوردة فائقة النعومة ومريحة للاستخدام اليومي.',
            'description_en' => 'Super-soft Egyptian and imported cottons for summer and comfort.',
            'is_active' => true,
            'sort_order' => 3,
        ]);
        $cotton->media()->create([
            'source' => 'external',
            'external_url' => 'https://images.unsplash.com/photo-1528458909336-e7a0adfed0a5?w=800&auto=format&fit=crop&q=80',
            'alt_text_ar' => 'كتان طبيعي عالي الجودة',
            'alt_text_en' => 'Premium Natural Linen',
            'is_primary' => true,
        ]);

        // 4. Sample Products
        $p1 = Product::create([
            'category_id' => $italianSilk->id,
            'name_ar' => 'حرير إيطالي كريب دي شين - كحلي ملكي',
            'name_en' => 'Italian Crepe de Chine Silk - Royal Navy',
            'slug_ar' => 'italian-crepe-de-chine-silk-royal-navy-ar',
            'slug_en' => 'italian-crepe-de-chine-silk-royal-navy',
            'sku' => 'SLK-IT-101',
            'short_description_ar' => 'قماش حرير إيطالي نقي 100% بلون كحلي ملكي جذاب ولمسة انسيابية فائقة.',
            'short_description_en' => '100% pure Italian silk with a fluid drape and lustrous royal navy finish.',
            'description_ar' => 'يتميز هذا القماش بنسيجه الدقيق ومقاومته للتجعد، مثالي لفساتين السهرة والبدل الراقية. العرض: 140 سم. الوزن: خفيف إلى متوسط.',
            'description_en' => 'Features a delicate texture and natural wrinkle resistance. Ideal for evening wear and luxury suits. Width: 140 cm.',
            'price' => 1250.00,
            'status' => 'published',
            'is_featured' => true,
            'sort_order' => 1,
            'published_at' => now(),
        ]);
        $p1->media()->create([
            'source' => 'external',
            'external_url' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=800&auto=format&fit=crop&q=80',
            'alt_text_ar' => 'حرير كريب دي شين كحلي',
            'alt_text_en' => 'Crepe de Chine Silk Royal Navy',
            'is_primary' => true,
            'sort_order' => 1,
        ]);
        $p1->media()->create([
            'source' => 'external',
            'external_url' => 'https://images.unsplash.com/photo-1617038220319-276d3cfab638?w=800&auto=format&fit=crop&q=80',
            'alt_text_ar' => 'تفاصيل نسيج الحرير',
            'alt_text_en' => 'Silk Texture Detail',
            'is_primary' => false,
            'sort_order' => 2,
        ]);

        $p2 = Product::create([
            'category_id' => $wool->id,
            'name_ar' => 'صوف إنجليزي فائق الجودة سوبر 150 - رمادي فحمي',
            'name_en' => 'English Super 150s Pure Wool - Charcoal Grey',
            'slug_ar' => 'english-super-150s-pure-wool-charcoal-ar',
            'slug_en' => 'english-super-150s-pure-wool-charcoal',
            'sku' => 'WOL-EN-204',
            'short_description_ar' => 'صوف طبيعي خام 100% من أشهر المغازل الإنجليزية المتخصصة في بدل كبار الشخصيات.',
            'short_description_en' => '100% pure virgin wool sourced from premier English mills for executive bespoke suiting.',
            'description_ar' => 'يمتاز هذا النسيج بالمتانة العالية والمظهر الملكي الأنيق الملائم لجميع الفصول. العرض: 150 سم.',
            'description_en' => 'High durability, breathable weave suitable for all-season tailoring. Width: 150 cm.',
            'price' => 2100.00,
            'status' => 'published',
            'is_featured' => true,
            'sort_order' => 2,
            'published_at' => now(),
        ]);
        $p2->media()->create([
            'source' => 'external',
            'external_url' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=800&auto=format&fit=crop&q=80',
            'alt_text_ar' => 'صوف إنجليزي رمادي فحمي',
            'alt_text_en' => 'English Charcoal Wool',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $p3 = Product::create([
            'category_id' => $cotton->id,
            'name_ar' => 'كتان إيرلندي طبيعي فاخر - أبيض ناصع',
            'name_en' => 'Pure Irish Linen - Crisp White',
            'slug_ar' => 'pure-irish-linen-crisp-white-ar',
            'slug_en' => 'pure-irish-linen-crisp-white',
            'sku' => 'LIN-IR-305',
            'short_description_ar' => 'قماش كتان أصيل بملمس منعش ومسامي يمنحك الراحة في الأجواء الحارة.',
            'short_description_en' => 'Authentic natural linen weave offering cooling breathability and crisp texture.',
            'description_ar' => 'الخيار الأول للقمصان الصيفية والعبايات الفاخرة والبدل الكاجوال الصيفية. العرض: 145 سم.',
            'description_en' => 'Premier choice for bespoke summer shirts, casual jackets, and flowing attire. Width: 145 cm.',
            'price' => 890.00,
            'status' => 'published',
            'is_featured' => true,
            'sort_order' => 3,
            'published_at' => now(),
        ]);
        $p3->media()->create([
            'source' => 'external',
            'external_url' => 'https://images.unsplash.com/photo-1528458909336-e7a0adfed0a5?w=800&auto=format&fit=crop&q=80',
            'alt_text_ar' => 'كتان أبيض ناصع',
            'alt_text_en' => 'Crisp White Linen',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        // 5. Marketing Collections
        $vipCollection = Collection::create([
            'name_ar' => 'مجموعة كبار الشخصيات VIP',
            'name_en' => 'VIP Executive Selection',
            'slug_ar' => 'vip-executive-selection-ar',
            'slug_en' => 'vip-executive-selection',
            'description_ar' => 'تشكيلة حصرية من أندر وأفخم الأقمشة المستوردة خصيصاً لأصحاب الذوق الرفيع.',
            'description_en' => 'Exclusive edit of rare and luxurious textiles curated for discerning patrons.',
            'status' => 'published',
            'is_featured' => true,
            'sort_order' => 1,
            'published_at' => now(),
        ]);
        $vipCollection->media()->create([
            'source' => 'external',
            'external_url' => 'https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1200&auto=format&fit=crop&q=80',
            'alt_text_ar' => 'مجموعة VIP',
            'alt_text_en' => 'VIP Selection Banner',
            'is_primary' => true,
        ]);
        $vipCollection->products()->attach([$p1->id => ['sort_order' => 1], $p2->id => ['sort_order' => 2]]);

        $summerCollection = Collection::create([
            'name_ar' => 'تشكيلة صيف 2026 للأقمشة الخفيفة',
            'name_en' => 'Summer 2026 Lightweight Edit',
            'slug_ar' => 'summer-2026-lightweight-edit-ar',
            'slug_en' => 'summer-2026-lightweight-edit',
            'description_ar' => 'أقمشة كتان وأقطان وحرائر خفيفة ومنعشة بتدرجات ألوان حيوية تناسب فصل الصيف.',
            'description_en' => 'Breathable linens, fine cottons, and featherlight silks designed for summer.',
            'status' => 'published',
            'is_featured' => true,
            'sort_order' => 2,
            'published_at' => now(),
        ]);
        $summerCollection->media()->create([
            'source' => 'external',
            'external_url' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=1200&auto=format&fit=crop&q=80',
            'alt_text_ar' => 'تشكيلة الصيف',
            'alt_text_en' => 'Summer Collection Banner',
            'is_primary' => true,
        ]);
        $summerCollection->products()->attach([$p3->id => ['sort_order' => 1], $p1->id => ['sort_order' => 2]]);

        // 6. Hero Sliders
        $slider1 = Slider::create([
            'title_ar' => 'أصالة النسيج وفخامة المظهر',
            'title_en' => 'Timeless Heritage, Unmatched Elegance',
            'subtitle_ar' => 'اكتشف أرقى أقمشة الصوف والحرير والكتان الإيطالي والإنجليزي لدى سلطان الأقمشة',
            'subtitle_en' => 'Discover premier Italian and English silks, wools, and linens at Sultan Fabrics',
            'button_text_ar' => 'استكشف المجموعات',
            'button_text_en' => 'Explore Collections',
            'button_url' => '/collections',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $slider1->media()->create([
            'source' => 'external',
            'external_url' => 'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1600&auto=format&fit=crop&q=80',
            'is_primary' => true,
        ]);

        // 7. Homepage Sections Configuration
        HomeSection::create([
            'section_key' => 'hero_slider',
            'title_ar' => 'شريط البانر الترويجي',
            'title_en' => 'Hero Banner Slider',
            'configuration' => ['autoplay' => true, 'interval_ms' => 5000],
            'is_active' => true,
            'sort_order' => 1,
        ]);
        HomeSection::create([
            'section_key' => 'featured_collections',
            'title_ar' => 'مجموعات مميزة',
            'title_en' => 'Featured Collections',
            'configuration' => ['limit' => 4, 'columns' => 2],
            'is_active' => true,
            'sort_order' => 2,
        ]);
        HomeSection::create([
            'section_key' => 'featured_products',
            'title_ar' => 'أحدث الأقمشة المختارة',
            'title_en' => 'Featured Textile Picks',
            'configuration' => ['limit' => 8, 'columns' => 4],
            'is_active' => true,
            'sort_order' => 3,
        ]);
        HomeSection::create([
            'section_key' => 'brand_story',
            'title_ar' => 'قصة سلطان الأقمشة',
            'title_en' => 'The Sultan Fabrics Story',
            'configuration' => ['show_stats' => true],
            'is_active' => true,
            'sort_order' => 4,
        ]);
        HomeSection::create([
            'section_key' => 'craftsmanship_gallery',
            'title_ar' => 'معرض الحرفية والتطريز',
            'title_en' => 'Craftsmanship & Photography Gallery',
            'configuration' => ['limit' => 6],
            'is_active' => true,
            'sort_order' => 5,
        ]);

        // 8. Core CMS Pages
        Page::create([
            'title_ar' => 'من نحن — سلطان الأقمشة',
            'title_en' => 'About Us — Sultan Fabrics',
            'slug_ar' => 'about-us-ar',
            'slug_en' => 'about-us',
            'content_ar' => '<p>سلطان الأقمشة هو صرح رائد في عالم الأقمشة الفاخرة، يجمع بين الأصالة العريقة والذوق الرفيع. نسعى دائماً لتوفير أجود أنواع المنسوجات من مختلف أنحاء العالم.</p>',
            'content_en' => '<p>Sultan Fabrics is a prestigious house of fine textiles, combining heritage craftsmanship with modern elegance. We curate premier fabrics from world-renowned mills.</p>',
            'meta_title_ar' => 'من نحن | سلطان الأقمشة',
            'meta_title_en' => 'About Us | Sultan Fabrics',
            'meta_description_ar' => 'تعرف على قصة سلطان الأقمشة ورسالتنا في تقديم أفخر خامات الأقمشة في المنطقة.',
            'meta_description_en' => 'Discover the heritage of Sultan Fabrics and our commitment to luxury textile excellence.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        Page::create([
            'title_ar' => 'دليل المقاسات والأقمشة',
            'title_en' => 'Fabric & Size Guide',
            'slug_ar' => 'size-guide-ar',
            'slug_en' => 'size-guide',
            'content_ar' => '<p>دليل شامل لاحتساب أطوال الأقمشة المطلوبة لتفصيل البدل والقمصان والعبايات بحسب عرض القماش ونوع التفصيل.</p>',
            'content_en' => '<p>A comprehensive yardage guide helping you calculate fabric requirements for bespoke suits, shirts, and traditional garments.</p>',
            'meta_title_ar' => 'دليل المقاسات | سلطان الأقمشة',
            'meta_title_en' => 'Fabric & Size Guide | Sultan Fabrics',
            'status' => 'published',
            'published_at' => now(),
        ]);

        // 9. Gallery Items
        $g1 = GalleryItem::create([
            'title_ar' => 'فحص جودة خيوط الحرير بالمشغل',
            'title_en' => 'Silk Yarn Quality Inspection',
            'description_ar' => 'لقطة توثق معايير التدقيق الدقيقة في فرز خيوط الحرير قبل الحياكة.',
            'description_en' => 'Close inspection of fine silk weave filaments prior to bespoke tailoring.',
            'is_active' => true,
            'sort_order' => 1,
        ]);
        $g1->media()->create([
            'source' => 'external',
            'external_url' => 'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=800&auto=format&fit=crop&q=80',
            'is_primary' => true,
        ]);
    }
}
