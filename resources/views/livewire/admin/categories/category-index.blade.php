<div>
    <!-- Top Actions & Filters -->
    <div class="bg-white p-4 rounded-lg border border-gray-200 shadow-sm mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex flex-1 items-center space-x-3 rtl:space-x-reverse">
            <div class="relative flex-1 max-w-sm">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search categories..."
                       class="w-full pl-3 pr-3 py-1.5 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
            </div>

            <select wire:model.live="filterParent" class="py-1.5 px-3 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
                <option value="">All Categories</option>
                <option value="root">Root Categories Only</option>
                @foreach($parentCategories as $p)
                    <option value="{{ $p->id }}">{{ $p->name_en }} ({{ $p->name_ar }})</option>
                @endforeach
            </select>
        </div>

        <button wire:click="openCreateModal"
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-900 hover:bg-slate-800 transition">
            + Add Category
        </button>
    </div>

    <!-- Categories Data Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left rtl:text-right">
                <tr>
                    <th class="px-4 py-3">Image</th>
                    <th class="px-4 py-3 cursor-pointer" wire:click="sortBy('name_en')">
                        English Name
                        @if($sortField === 'name_en') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                    </th>
                    <th class="px-4 py-3">Arabic Name</th>
                    <th class="px-4 py-3">Parent</th>
                    <th class="px-4 py-3 text-center">Products</th>
                    <th class="px-4 py-3 text-center cursor-pointer" wire:click="sortBy('sort_order')">
                        Order
                        @if($sortField === 'sort_order') <span>{{ $sortDirection === 'asc' ? '↑' : '↓' }}</span> @endif
                    </th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right rtl:text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($categories as $category)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="px-4 py-3">
                            <img src="{{ $category->image_url }}" alt="{{ $category->name_en }}"
                                 class="w-10 h-10 object-cover rounded border border-gray-200 bg-gray-100">
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $category->name_en }}
                            <div class="text-[11px] text-gray-400 font-mono">{{ $category->slug_en }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $category->name_ar }}
                            <div class="text-[11px] text-gray-400 font-mono">{{ $category->slug_ar }}</div>
                        </td>
                        <td class="px-4 py-3 text-gray-500">
                            {{ $category->parent ? $category->parent->name_en : '— Root' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                {{ $category->products_count }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-500 font-mono text-xs">
                            {{ $category->sort_order }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="toggleActive({{ $category->id }})"
                                    class="px-2.5 py-0.5 rounded text-xs font-semibold transition {{ $category->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $category->is_active ? 'Active' : 'Disabled' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right rtl:text-left space-x-2 rtl:space-x-reverse font-medium">
                            <button wire:click="openEditModal({{ $category->id }})" class="text-slate-600 hover:text-slate-900">Edit</button>
                            <button wire:click="deleteCategory({{ $category->id }})"
                                    wire:confirm="Are you sure you want to delete this category?"
                                    class="text-rose-600 hover:text-rose-900">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                            No categories found matching your query.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-gray-200">
            {{ $categories->links() }}
        </div>
    </div>

    <!-- Category Modal (Create / Edit) -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-lg border border-gray-200 shadow-xl max-w-2xl w-full p-6" @click.outside="$wire.closeModal()">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200 mb-4">
                    <h3 class="text-lg font-bold text-gray-900">
                        {{ $isEditing ? 'Edit Category' : 'Create New Category' }}
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700">Parent Category</label>
                        <select wire:model="form.parent_id" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            <option value="">None (Top-Level Category)</option>
                            @foreach($parentCategories as $p)
                                @if(!$isEditing || $p->id !== $editingId)
                                    <option value="{{ $p->id }}">{{ $p->name_en }} ({{ $p->name_ar }})</option>
                                @endif
                            @endforeach
                        </select>
                        @error('form.parent_id') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Name (English)</label>
                            <input type="text" wire:model.live.debounce.400ms="form.name_en"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            @error('form.name_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Name (Arabic)</label>
                            <input type="text" wire:model.live.debounce.400ms="form.name_ar" dir="rtl"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                            @error('form.name_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Slug (English)</label>
                            <input type="text" wire:model="form.slug_en" placeholder="auto-generated from name"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono">
                            @error('form.slug_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Slug (Arabic)</label>
                            <input type="text" wire:model="form.slug_ar" placeholder="auto-generated from name" dir="rtl"
                                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm font-mono">
                            @error('form.slug_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Description (English)</label>
                            <textarea wire:model="form.description_en" rows="2" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm"></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Description (Arabic)</label>
                            <textarea wire:model="form.description_ar" rows="2" dir="rtl" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm"></textarea>
                        </div>
                    </div>

                    <!-- Media Attachment -->
                    <div class="border border-gray-200 rounded-md p-3 bg-gray-50">
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-2">Category Thumbnail</label>
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
                            <input type="url" wire:model="form.image_url" placeholder="https://cdn.example.com/category.jpg"
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
                                <input type="checkbox" wire:model="form.is_active" class="rounded text-slate-900">
                                <span class="ml-2">Active</span>
                            </label>

                            <div class="flex items-center space-x-2">
                                <label class="text-xs text-gray-500">Sort Order:</label>
                                <input type="number" wire:model="form.sort_order" class="w-20 px-2 py-1 border border-gray-300 rounded text-sm">
                            </div>
                        </div>

                        <div class="space-x-2">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 border border-gray-300 rounded-md text-sm text-gray-700 hover:bg-gray-50">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-md text-sm font-medium hover:bg-slate-800">
                                {{ $isEditing ? 'Save Changes' : 'Create Category' }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
