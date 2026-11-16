<?php

namespace App\Livewire\Admin\Dashboard;

use App\Models\Category;
use App\Models\Collection;
use App\Models\ContactMessage;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard Overview')]
class Overview extends Component
{
    public function render()
    {
        $this->authorize('viewAny', Product::class);

        return view('livewire.admin.dashboard.overview', [
            'totalProducts' => Product::count(),
            'publishedProducts' => Product::published()->count(),
            'totalCategories' => Category::count(),
            'totalCollections' => Collection::count(),
            'unreadInquiries' => ContactMessage::unread()->count(),
            'recentInquiries' => ContactMessage::latest()->take(5)->get(),
            'featuredProducts' => Product::with(['category', 'primaryImage'])->featured()->take(4)->get(),
        ]);
    }
}
