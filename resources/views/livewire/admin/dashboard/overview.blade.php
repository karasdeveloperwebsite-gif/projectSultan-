<div>
    <!-- Metric Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Products</div>
            <div class="mt-2 flex items-baseline justify-between">
                <div class="text-2xl font-bold text-gray-900">{{ $totalProducts }}</div>
                <span class="text-xs text-emerald-600 font-medium">{{ $publishedProducts }} Published</span>
            </div>
            <a href="{{ route('admin.products') }}" class="mt-3 block text-xs text-slate-600 hover:text-slate-900 font-medium">Manage Products &rarr;</a>
        </div>

        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Categories</div>
            <div class="mt-2 text-2xl font-bold text-gray-900">{{ $totalCategories }}</div>
            <a href="{{ route('admin.categories') }}" class="mt-3 block text-xs text-slate-600 hover:text-slate-900 font-medium">Manage Categories &rarr;</a>
        </div>

        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Collections</div>
            <div class="mt-2 text-2xl font-bold text-gray-900">{{ $totalCollections }}</div>
            <a href="{{ route('admin.collections') }}" class="mt-3 block text-xs text-slate-600 hover:text-slate-900 font-medium">Manage Collections &rarr;</a>
        </div>

        <div class="bg-white p-5 rounded-lg border border-gray-200 shadow-sm">
            <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Inquiries</div>
            <div class="mt-2 flex items-baseline justify-between">
                <div class="text-2xl font-bold text-gray-900">{{ $unreadInquiries }}</div>
                @if($unreadInquiries > 0)
                    <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 font-bold">Needs Attention</span>
                @else
                    <span class="text-xs text-gray-400">All caught up</span>
                @endif
            </div>
            <a href="{{ route('admin.inquiries') }}" class="mt-3 block text-xs text-slate-600 hover:text-slate-900 font-medium">View Inbox &rarr;</a>
        </div>
    </div>

    <!-- Quick Action / Status Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Inquiries -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3">
                <h3 class="font-bold text-gray-800 text-sm">Recent Customer Inquiries</h3>
                <a href="{{ route('admin.inquiries') }}" class="text-xs text-slate-600 hover:underline">View All</a>
            </div>

            @if($recentInquiries->isEmpty())
                <p class="text-sm text-gray-400 py-6 text-center">No inquiries received yet.</p>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($recentInquiries as $msg)
                        <div class="py-2.5 flex items-center justify-between">
                            <div>
                                <div class="font-medium text-sm text-gray-800">{{ $msg->name }}</div>
                                <div class="text-xs text-gray-500">{{ $msg->phone ?: $msg->email }}</div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs px-2 py-0.5 rounded {{ $msg->status === 'new' ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-600' }}">
                                    {{ ucfirst($msg->status) }}
                                </span>
                                <div class="text-[11px] text-gray-400 mt-1">{{ $msg->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Featured Products -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-5">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 mb-3">
                <h3 class="font-bold text-gray-800 text-sm">Featured Showroom Fabrics</h3>
                <a href="{{ route('admin.products') }}" class="text-xs text-slate-600 hover:underline">View All</a>
            </div>

            @if($featuredProducts->isEmpty())
                <p class="text-sm text-gray-400 py-6 text-center">No featured products selected.</p>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($featuredProducts as $item)
                        <div class="py-2 flex items-center space-x-3 rtl:space-x-reverse">
                            <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-10 h-10 object-cover rounded bg-gray-100 border border-gray-200">
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-sm text-gray-800 truncate">{{ $item->name_en }}</div>
                                <div class="text-xs text-gray-500">{{ $item->category->name_en ?? 'Uncategorized' }}</div>
                            </div>
                            <div class="text-sm font-semibold text-gray-900">
                                {{ number_format($item->price, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
