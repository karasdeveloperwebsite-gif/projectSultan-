<div>
    <div class="mb-6">
        <h2 class="text-base font-semibold text-gray-900">Homepage Dynamic Sections</h2>
        <p class="text-xs text-gray-500">Control the display order and active visibility of sections rendered on the digital showroom homepage.</p>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left rtl:text-right">
                <tr>
                    <th class="px-4 py-3">Section Identifier</th>
                    <th class="px-4 py-3">Title (EN)</th>
                    <th class="px-4 py-3">Title (AR)</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Order</th>
                    <th class="px-4 py-3 text-right rtl:text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($sections as $section)
                    <tr class="hover:bg-gray-50/60 transition">
                        <td class="px-4 py-3 font-mono font-medium text-xs text-slate-800">
                            {{ $section->section_key }}
                        </td>
                        <td class="px-4 py-3 text-gray-900 font-medium">
                            {{ $section->title_en ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-700">
                            {{ $section->title_ar ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="toggleActive({{ $section->id }})"
                                    class="text-xs px-2.5 py-0.5 rounded font-semibold transition {{ $section->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">
                                {{ $section->is_active ? 'Active' : 'Disabled' }}
                            </button>
                        </td>
                        <td class="px-4 py-3 text-center text-xs font-mono text-gray-500">
                            {{ $section->sort_order }}
                        </td>
                        <td class="px-4 py-3 text-right rtl:text-left space-x-1 rtl:space-x-reverse">
                            <button wire:click="moveUp({{ $section->id }})" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs">&uarr; Up</button>
                            <button wire:click="moveDown({{ $section->id }})" class="px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded text-xs">&darr; Down</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
