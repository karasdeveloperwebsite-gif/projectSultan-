<div>
    <!-- Top Filters & Actions -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-1 items-center space-x-3 rtl:space-x-reverse">
            <div class="relative flex-1 max-w-sm">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search collections..."
                       class="w-full px-3 py-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
            </div>

            <select wire:model.live="filterStatus" class="py-1.5 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
                <option value="">All Statuses</option>
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="archived">Archived</option>
            </select>
        </div>

        <button wire:click="openCreateModal"
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-900 hover:bg-slate-800 transition">
            + Add Collection
        </button>
    </div>

    <!-- Collections Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left rtl:text-right">
                <tr>
                    <th class="px-4 py-3">Banner</th>
                    <th class="px-4 py-3 cursor-pointer" wire:click="sortBy('name_en')">
                        Collection Name
                        @if($sortField === 'name_en') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                    </th>
                    <th class="px-4 py-3">Arabic Name</th>
                    <th class="px-4 py-3 text-center">Assigned Fabrics</th>
                    <th class="px-4 py-3 text-center">Featured</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center cursor-pointer" wire:click="sortBy('sort_order')">
                        Order
                        @if($sortField === 'sort_order') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                    </th>
                    <th class="px-4 py-3 text-right rtl:text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($collections as $col)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="px-4 py-3">
                            <img src="{{ $col->image_url }}" alt="{{ $col->name_en }}"
                                 class="w-14 h-9 object-cover rounded border border-gray-200 bg-gray-100">
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">
                            <div>{{ $col->name_en }}</div>
                            <div class="text-[11px] text-gray-400 font-mono">{{ $col->slug_en }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            <div>{{ $col->name_ar }}</div>
                            <div class="text-[11px] text-gray-400 font-mono">{{ $col->slug_ar }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                {{ $col->products_count }} Products
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="toggleFeatured({{ $col->id }})"
                                    class="text-xs px-2 py-0.5 rounded font-semibold transition {{ $col->is_featured ? 'bg-amber-100 text-amber-800' : 'bg-gray-100 text-gray-400' }}">
                                {{ $col->is_featured ? '★ Featured' : '☆ Normal' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs px-2 py-0.5 rounded font-semibold {{ $col->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ ucfirst($col->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-500 font-mono">
                            {{ $col->sort_order }}
                        </td>
                        <td class="px-4 py-3 text-right rtl:text-left space-x-2 rtl:space-x-reverse font-medium">
                            <button wire:click="openEditModal({{ $col->id }})" class="text-slate-600 hover:text-slate-900">Edit</button>
                            <button wire:click="deleteCollection({{ $col->id }})"
                                    wire:confirm="Are you sure you want to delete this collection?"
                                    class="text-rose-600 hover:text-rose-900">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            No marketing collections found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-gray-200">
            {{ $collections->links() }}
        </div>
    </div>

    <!-- Collection Modal (Create / Edit) -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-lg border border-gray-200 shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-y-auto" @click.outside="$wire.closeModal()">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200 mb-4">
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ $isEditing ? 'Edit Collection' : 'Create New Collection' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-4">
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
                            <input type="text" wire:model="form.slug_en" placeholder="auto-generated"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono">
                            @error('form.slug_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Slug (Arabic) *</label>
                            <input type="text" wire:model="form.slug_ar" placeholder="auto-generated" dir="rtl"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono">
                            @error('form.slug_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Campaign Blurb (EN)</label>
                            <textarea wire:model="form.description_en" rows="2" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Campaign Blurb (AR)</label>
                            <textarea wire:model="form.description_ar" rows="2" dir="rtl" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm"></textarea>
                        </div>
                    </div>

                    <!-- Assign Products -->
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-1">Assign Products to this Collection</label>
                        <div class="grid grid-cols-2 gap-2 p-2 border border-gray-200 rounded-md bg-gray-50 max-h-36 overflow-y-auto">
                            @foreach($availableProducts as $prod)
                                <label class="flex items-center text-xs text-gray-700">
                                    <input type="checkbox" value="{{ $prod->id }}" wire:model="form.product_ids" class="rounded text-slate-900">
                                    <span class="ml-1.5 truncate">{{ $prod->name_en }} ({{ $prod->category->name_en ?? '—' }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Media Banner -->
                    <div class="border border-gray-200 rounded-md p-3 bg-gray-50">
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-2">Collection Banner Image</label>
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
                            <input type="url" wire:model="form.image_url" placeholder="https://cdn.example.com/collection-banner.jpg"
                                   class="block w-full px-3 py-1.5 border border-gray-300 rounded-md text-sm">
                            @error('form.image_url') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        @else
                            <input type="file" wire:model="form.image_file" accept="image/*"
                                   class="block w-full text-xs text-gray-500 file:mr-4 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-slate-900 file:text-white hover:file:bg-slate-800">
                            @error('form.image_file') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center text-sm text-gray-700">
                                <input type="checkbox" wire:model="form.is_featured" class="rounded text-slate-900">
                                <span class="ml-2">Feature on Home</span>
                            </label>

                            <select wire:model="form.status" class="py-1 px-2 border rounded text-xs">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                                <option value="archived">Archived</option>
                            </select>

                            <div class="flex items-center space-x-1">
                                <span class="text-xs text-gray-500">Order:</span>
                                <input type="number" wire:model="form.sort_order" class="w-16 px-2 py-1 border rounded text-xs">
                            </div>
                        </div>

                        <div class="space-x-2">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-md text-sm font-medium hover:bg-slate-800">
                                {{ $isEditing ? 'Save Changes' : 'Create Collection' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
