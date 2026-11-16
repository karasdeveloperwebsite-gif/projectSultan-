<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Hero Section Banners</h2>
            <p class="text-xs text-gray-500">Manage promotional slides displayed at the top of the digital showroom.</p>
        </div>
        <button wire:click="openCreateModal"
                class="px-4 py-2 bg-slate-900 text-white rounded-md text-sm font-medium hover:bg-slate-800 transition">
            + Add Slide
        </button>
    </div>

    <!-- Sliders Table -->
    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left rtl:text-right">
                <tr>
                    <th class="px-4 py-3">Slide Image</th>
                    <th class="px-4 py-3">Heading (EN)</th>
                    <th class="px-4 py-3">Heading (AR)</th>
                    <th class="px-4 py-3">CTA Link</th>
                    <th class="px-4 py-3 text-center">Order</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-right rtl:text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($sliders as $slider)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="px-4 py-3">
                            <img src="{{ $slider->image_url }}" alt="{{ $slider->title_en }}"
                                 class="w-16 h-9 object-cover rounded border border-gray-200 bg-gray-100">
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-900">
                            {{ $slider->title_en ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $slider->title_ar ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 font-mono">
                            {{ $slider->button_url ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-500 font-mono">
                            {{ $slider->sort_order }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="toggleActive({{ $slider->id }})"
                                    class="text-xs px-2.5 py-0.5 rounded font-semibold transition {{ $slider->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $slider->is_active ? 'Active' : 'Hidden' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right rtl:text-left space-x-2 rtl:space-x-reverse font-medium">
                            <button wire:click="openEditModal({{ $slider->id }})" class="text-slate-600 hover:text-slate-900">Edit</button>
                            <button wire:click="deleteSlider({{ $slider->id }})" wire:confirm="Delete this slide?" class="text-rose-600 hover:text-rose-900">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">No slides created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-lg border border-gray-200 shadow-xl max-w-xl w-full p-6" @click.outside="$wire.closeModal()">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200 mb-4">
                    <h3 class="text-lg font-bold text-gray-900">{{ $isEditing ? 'Edit Slide' : 'New Slide' }}</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Headline (EN)</label>
                            <input type="text" wire:model="form.title_en" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                            @error('form.title_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Headline (AR)</label>
                            <input type="text" wire:model="form.title_ar" dir="rtl" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                            @error('form.title_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Subtitle (EN)</label>
                            <input type="text" wire:model="form.subtitle_en" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                            @error('form.subtitle_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">Subtitle (AR)</label>
                            <input type="text" wire:model="form.subtitle_ar" dir="rtl" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                            @error('form.subtitle_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">CTA Button Text (EN)</label>
                            <input type="text" wire:model="form.button_text_en" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                            @error('form.button_text_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold uppercase text-gray-700">CTA Button Text (AR)</label>
                            <input type="text" wire:model="form.button_text_ar" dir="rtl" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                            @error('form.button_text_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-700">Button Destination URL</label>
                        <input type="text" wire:model="form.button_url" placeholder="/collections or https://..."
                               class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm font-mono">
                        @error('form.button_url') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </div>

                    <!-- Slide Image -->
                    <div class="border border-gray-200 rounded-md p-3 bg-gray-50">
                        <label class="block text-xs font-semibold uppercase text-gray-700 mb-2">Slide Image</label>
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
                            <input type="url" wire:model="form.image_url" placeholder="https://cdn.example.com/banner.jpg"
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
                            <button type="submit" class="px-4 py-2 bg-slate-900 text-white rounded-md text-sm font-medium">Save Slide</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
