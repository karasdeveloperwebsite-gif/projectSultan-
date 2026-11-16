<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Craftsmanship & Photography Gallery</h2>
            <p class="text-xs text-gray-500">Showcase fabric textures, tailoring workshop highlights, and client stories.</p>
        </div>
        <button wire:click="openCreateModal"
                class="px-4 py-2 bg-slate-900 text-white rounded-md text-sm font-medium hover:bg-slate-800 transition">
            + Add Photo
        </button>
    </div>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        @forelse($items as $item)
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm flex flex-col justify-between">
                <div>
                    <img src="{{ $item->image_url }}" alt="{{ $item->title_en }}" class="w-full h-40 object-cover bg-gray-100">
                    <div class="p-3">
                        <div class="font-medium text-sm text-gray-900 truncate">{{ $item->title_en ?: 'Untitled' }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ $item->title_ar ?: '—' }}</div>
                    </div>
                </div>

                <div class="p-3 border-t border-gray-100 flex items-center justify-between text-xs bg-gray-50">
                    <button wire:click="toggleActive({{ $item->id }})"
                            class="px-2 py-0.5 rounded font-semibold {{ $item->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-200 text-gray-600' }}">
                        {{ $item->is_active ? 'Visible' : 'Hidden' }}
                    </button>
                    <div class="space-x-2 rtl:space-x-reverse">
                        <button wire:click="openEditModal({{ $item->id }})" class="text-slate-600 hover:text-slate-900 font-medium">Edit</button>
                        <button wire:click="deleteItem({{ $item->id }})" wire:confirm="Delete this photo?" class="text-rose-600 hover:text-rose-900 font-medium">Delete</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center text-gray-400">
                No gallery photos added yet.
            </div>
        @endforelse
    </div>

    {{ $items->links() }}

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-lg border border-gray-200 shadow-xl max-w-lg w-full p-6" @click.outside="$wire.closeModal()">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200 mb-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ $isEditing ? 'Edit Photo' : 'Add Photo' }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700">Title (English)</label>
                        <input type="text" wire:model="form.title_en" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                        @error('form.title_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700">Title (Arabic)</label>
                        <input type="text" wire:model="form.title_ar" dir="rtl" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                        @error('form.title_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700">Caption / Notes (English)</label>
                        <textarea wire:model="form.description_en" rows="2" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm"></textarea>
                        @error('form.description_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700">Caption / Notes (Arabic)</label>
                        <textarea wire:model="form.description_ar" rows="2" dir="rtl" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm"></textarea>
                        @error('form.description_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <!-- Media -->
                    <div class="border border-gray-200 rounded-md p-3 bg-gray-50">
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-2">Image Source</label>
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
                            <input type="url" wire:model="form.image_url" placeholder="https://cdn.example.com/craft.jpg"
                                   class="block w-full px-3 py-1.5 border rounded-md text-sm">
                            @error('form.image_url') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        @else
                            <input type="file" wire:model="form.image_file" accept="image/*"
                                   class="block w-full text-xs text-gray-500 file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-xs file:bg-slate-900 file:text-white">
                            @error('form.image_file') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        @endif
                    </div>

                    <div class="flex items-center justify-between pt-2">
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center text-sm">
                                <input type="checkbox" wire:model="form.is_active" class="rounded text-slate-900">
                                <span class="ml-2">Active</span>
                            </label>
                            <div class="flex items-center space-x-1">
                                <span class="text-xs text-gray-500">Order:</span>
                                <input type="number" wire:model="form.sort_order" class="w-16 px-2 py-1 border rounded text-xs">
                            </div>
                        </div>

                        <div class="space-x-2">
                            <button type="button" wire:click="closeModal" class="px-4 py-2 border rounded-md text-sm text-gray-700">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-md text-sm font-medium">Save Photo</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
