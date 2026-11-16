<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Collection;
use App\Models\ContactMessage;
use App\Models\GalleryItem;
use App\Models\HomeSection;
use App\Models\Page;
use App\Models\Product;
use App\Models\Setting;
use App\Models\Slider;
use App\Policies\CategoryPolicy;
use App\Policies\CollectionPolicy;
use App\Policies\ContactMessagePolicy;
use App\Policies\GalleryItemPolicy;
use App\Policies\HomeSectionPolicy;
use App\Policies\PagePolicy;
use App\Policies\ProductPolicy;
use App\Policies\SettingPolicy;
use App\Policies\SliderPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Collection::class, CollectionPolicy::class);
        Gate::policy(Slider::class, SliderPolicy::class);
        Gate::policy(GalleryItem::class, GalleryItemPolicy::class);
        Gate::policy(Page::class, PagePolicy::class);
        Gate::policy(ContactMessage::class, ContactMessagePolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
        Gate::policy(HomeSection::class, HomeSectionPolicy::class);
    }
}
