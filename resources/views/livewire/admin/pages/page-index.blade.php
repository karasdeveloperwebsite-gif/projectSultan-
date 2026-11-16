<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Custom & Informational Pages</h2>
            <p class="text-xs text-gray-500">Manage About Us, Size Guides, Policies, and other dynamic content pages.</p>
        </div>
        <button wire:click="openCreateModal"
                class="px-4 py-2 bg-slate-900 text-white rounded-md text-sm font-medium hover:bg-slate-800 transition">
            + Add Page
        </button>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left rtl:text-right">
                <tr>
                    <th class="px-4 py-3">Page Title (EN)</th>
                    <th class="px-4 py-3">Page Title (AR)</th>
                    <th class="px-4 py-3">Slug (EN / AR)</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right rtl:text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($pages as $page)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $page->title_en }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $page->title_ar }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">
                            <div>/{{ $page->slug_en }}</div>
                            <div class="text-[11px] text-gray-400">/{{ $page->slug_ar }}</div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs px-2.5 py-0.5 rounded font-semibold {{ $page->status === 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ ucfirst($page->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right rtl:text-left space-x-2 rtl:space-x-reverse font-medium">
                            <button wire:click="openEditModal({{ $page->id }})" class="text-slate-600 hover:text-slate-900">Edit</button>
                            <button wire:click="deletePage({{ $page->id }})" wire:confirm="Delete this page?" class="text-rose-600 hover:text-rose-900">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-400">No pages found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-lg border border-gray-200 shadow-xl max-w-3xl w-full p-6 max-h-[90vh] overflow-y-auto" @click.outside="$wire.closeModal()">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200 mb-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ $isEditing ? 'Edit Page' : 'New Page' }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Title (English) *</label>
                            <input type="text" wire:model.live.debounce.400ms="form.title_en" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                            @error('form.title_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Title (Arabic) *</label>
                            <input type="text" wire:model.live.debounce.400ms="form.title_ar" dir="rtl" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                            @error('form.title_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Slug (English) *</label>
                            <input type="text" wire:model="form.slug_en" placeholder="auto-generated" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm font-mono">
                            @error('form.slug_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Slug (Arabic) *</label>
                            <input type="text" wire:model="form.slug_ar" placeholder="auto-generated" dir="rtl" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm font-mono">
                            @error('form.slug_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Page Content (English)</label>
                            <textarea wire:model="form.content_en" rows="6" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm font-mono"></textarea>
                            @error('form.content_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Page Content (Arabic)</label>
                            <textarea wire:model="form.content_ar" rows="6" dir="rtl" class="mt-1 block w-full px-3 py-2 border rounded-md text-sm font-mono"></textarea>
                            @error('form.content_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Meta Title (EN)</label>
                            <input type="text" wire:model="form.meta_title_en" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                            @error('form.meta_title_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Meta Title (AR)</label>
                            <input type="text" wire:model="form.meta_title_ar" dir="rtl" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                            @error('form.meta_title_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <select wire:model="form.status" class="py-1 px-3 border rounded text-sm">
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                        </select>

                        <div class="space-x-2">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 border rounded-md text-sm text-gray-700">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-md text-sm font-medium">Save Page</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
