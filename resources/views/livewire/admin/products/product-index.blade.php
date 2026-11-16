<div>
    <!-- Top Filters & Actions -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-1 flex-wrap items-center gap-3">
            <div class="relative flex-1 min-w-[200px] max-w-sm">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or SKU..."
                       class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
            </div>

            <select wire:model.live="filterCategory" class="py-1.5 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name_en }} ({{ $cat->name_ar }})</option>
                @endforeach
            </select>

            <select wire:model.live="filterStatus" class="py-1.5 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </select>
        </div>

        <button wire:click="openCreateModal"
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-900 hover:bg-slate-800 transition">
            + Add Product
        </button>
    </div>

    <!-- Products Data Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left rtl:text-right">
                <tr>
                    <th class="px-4 py-3">Thumbnail</th>
                    <th class="px-4 py-3 cursor-pointer" wire:click="sortBy('name_en')">
                        Product Name
                        @if($sortField === 'name_en') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                    </th>
                    <th class="px-4 py-3">SKU</th>
                    <th class="px-4 py-3">Category</th>
                    <th class="px-4 py-3 cursor-pointer" wire:click="sortBy('price')">
                        Price
                        @if($sortField === 'price') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                    </th>
                    <th class="px-4 py-3 text-center">Featured</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right rtl:text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($products as $product)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="px-4 py-3">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name_en }}"
                                 class="w-12 h-12 object-cover rounded border border-gray-200 bg-gray-100">
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">
                            <div>{{ $product->name_en }}</div>
                            <div class="text-xs text-gray-500">{{ $product->name_ar }}</div>
                            @if($product->collections->isNotEmpty())
                                <div class="mt-1 flex flex-wrap gap-1">
                                    @foreach($product->collections as $col)
                                        <span class="inline-block text-[10px] px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 font-mono">
                                            {{ $col->name_en }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">
                            {{ $product->sku ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $product->category->name_en ?? '—' }}
                        </td>
                        <td class="px-4 py-3 font-semibold text-gray-900">
                            {{ $product->price !== null ? number_format($product->price, 2) : 'Request' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="toggleFeatured({{ $product->id }})"
                                    class="text-xs px-2 py-0.5 rounded font-semibold transition {{ $product->is_featured ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-400' }}">
                                {{ $product->is_featured ? '★ Featured' : '☆ Normal' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <select wire:change="updateStatus({{ $product->id }}, $event.target.value)"
                                    class="text-xs py-1 px-2 border rounded font-semibold {{ $product->status === 'published' ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : ($product->status === 'draft' ? 'bg-amber-50 text-amber-800 border-amber-300' : 'bg-gray-50 text-gray-600 border-gray-300') }}">
                                <option value="draft" {{ $product->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ $product->status === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ $product->status === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </td>
                        <td class="px-4 py-3 text-right rtl:text-left space-x-2 rtl:space-x-reverse font-medium">
                            <button wire:click="openEditModal({{ $product->id }})" class="text-slate-600 hover:text-slate-900">Edit</button>
                            <button wire:click="deleteProduct({{ $product->id }})"
                                    wire:confirm="Are you sure you want to delete this product?"
                                    class="text-rose-600 hover:text-rose-900">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            No products found matching your search.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-gray-200">
            {{ $products->links() }}
        </div>
    </div>

    <!-- Product Modal (Create / Edit) -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-lg border border-gray-200 shadow-xl max-w-3xl w-full p-6 max-h-[90vh] overflow-y-auto" @click.outside="$wire.closeModal()">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200 mb-4">
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ $isEditing ? 'Edit Product' : 'Create New Product' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Category *</label>
                            <select wire:model="form.category_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name_en }} ({{ $cat->name_ar }})</option>
                                @endforeach
                            </select>
                            @error('form.category_id') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">SKU (Code)</label>
                            <input type="text" wire:model="form.sku" placeholder="e.g. SLK-101"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono">
                            @error('form.sku') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Name (English) *</label>
                            <input type="text" wire:model.live.debounce.400ms="form.name_en"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            @error('form.name_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Name (Arabic) *</label>
                            <input type="text" wire:model.live.debounce.400ms="form.name_ar" dir="rtl"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            @error('form.name_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Slug (English) *</label>
                            <input type="text" wire:model="form.slug_en" placeholder="auto-generated from name"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono">
                            @error('form.slug_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Slug (Arabic) *</label>
                            <input type="text" wire:model="form.slug_ar" placeholder="auto-generated from name" dir="rtl"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono">
                            @error('form.slug_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Price (Decimal)</label>
                            <input type="number" step="0.01" wire:model="form.price" placeholder="Leave empty for price on request"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            @error('form.price') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Status</label>
                            <select wire:model="form.status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Sort Order</label>
                            <input type="number" wire:model="form.sort_order" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                    </div>

                    <!-- Marketing Collections -->
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Tag In Marketing Collections</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 p-2 border border-gray-200 rounded-md bg-gray-50 max-h-32 overflow-y-auto">
                            @foreach($collections as $col)
                                <label class="flex items-center text-xs text-gray-700">
                                    <input type="checkbox" value="{{ $col->id }}" wire:model="form.collection_ids" class="rounded text-slate-900">
                                    <span class="ml-1.5 truncate">{{ $col->name_en }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Descriptions -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Short Summary (EN)</label>
                            <textarea wire:model="form.short_description_en" rows="2" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Short Summary (AR)</label>
                            <textarea wire:model="form.short_description_ar" rows="2" dir="rtl" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Full Details & Weave (EN)</label>
                            <textarea wire:model="form.description_en" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Full Details & Weave (AR)</label>
                            <textarea wire:model="form.description_ar" rows="3" dir="rtl" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm"></textarea>
                        </div>
                    </div>

                    <!-- Media Attachment -->
                    <div class="border border-gray-200 rounded-md p-3 bg-gray-50">
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-2">Primary Product Photo</label>
                        <div class="flex items-center space-x-4 mb-2 text-xs">
                            <label class="flex items-center">
                                <input type="radio" value="external" wire:model.live="form.image_source" class="text-slate-900">
                                <span class="ml-1.5">External Image URL</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" value="local" wire:model.live="form.image_source" class="text-slate-900">
                                <span class="ml-1.5">Upload Local File</span>
                            </label>
                        </div>

                        @if($form->image_source === 'external')
                            <input type="url" wire:model="form.image_url" placeholder="https://cdn.example.com/fabric-photo.jpg"
                                   class="block w-full px-3 py-1.5 border border-gray-300 rounded-md text-sm">
                            @error('form.image_url') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        @else
                            <input type="file" wire:model="form.image_file" accept="image/*"
                                   class="block w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800">
                            @error('form.image_file') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-3 border-t border-gray-200">
                        <label class="flex items-center text-sm text-gray-700">
                            <input type="checkbox" wire:model="form.is_featured" class="rounded text-slate-900">
                            <span class="ml-2">Feature on Homepage</span>
                        </label>

                        <div class="space-x-2">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-md text-sm font-medium hover:bg-slate-800">
                                {{ $isEditing ? 'Save Changes' : 'Create Product' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
