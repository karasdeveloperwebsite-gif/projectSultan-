<div>
    <div class="mb-6">
        <h2 class="text-base font-semibold text-gray-900">Global Website Settings</h2>
        <p class="text-xs text-gray-500">Configure business contact information, primary WhatsApp number, and social media channels.</p>
    </div>

    <!-- Tabs -->
    <div class="flex space-x-2 border-b border-gray-200 mb-6 text-sm">
        <button wire:click="$set('activeTab', 'general')"
                class="py-2 px-4 font-medium border-b-2 transition {{ $activeTab === 'general' ? 'border-slate-900 text-slate-900' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            General Brand
        </button>
        <button wire:click="$set('activeTab', 'contact')"
                class="py-2 px-4 font-medium border-b-2 transition {{ $activeTab === 'contact' ? 'border-slate-900 text-slate-900' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Contact & WhatsApp
        </button>
        <button wire:click="$set('activeTab', 'social')"
                class="py-2 px-4 font-medium border-b-2 transition {{ $activeTab === 'social' ? 'border-slate-900 text-slate-900' : 'border-transparent text-gray-500 hover:text-gray-700' }}">
            Social Media Channels
        </button>
    </div>

    <form wire:submit="save" class="bg-white p-6 rounded-lg border border-gray-200 shadow-sm max-w-3xl space-y-4">
        @if($activeTab === 'general')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700">Site Name (English) *</label>
                    <input type="text" wire:model="site_name_en" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                    @error('site_name_en') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700">Site Name (Arabic) *</label>
                    <input type="text" wire:model="site_name_ar" dir="rtl" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                    @error('site_name_ar') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700">Brand Tagline (English)</label>
                    <input type="text" wire:model="site_tagline_en" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700">Brand Tagline (Arabic)</label>
                    <input type="text" wire:model="site_tagline_ar" dir="rtl" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700">Default Currency Code</label>
                <input type="text" wire:model="currency_code" placeholder="EGP" class="mt-1 block w-32 px-3 py-1.5 border rounded-md text-sm font-mono">
                @error('currency_code') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
            </div>
        @elseif($activeTab === 'contact')
            <div class="p-3 bg-amber-50 border border-amber-200 rounded-md text-xs text-amber-800 mb-4">
                <strong>Important:</strong> The WhatsApp number below is used across the entire digital showroom to generate product inquiry click-to-chat links. Use international format without plus or spaces (e.g. <code>201000000000</code>).
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700">WhatsApp Inquiry Number *</label>
                    <input type="text" wire:model="whatsapp_number" placeholder="201000000000" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm font-mono">
                    @error('whatsapp_number') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700">Display Phone Number</label>
                    <input type="text" wire:model="contact_phone" placeholder="+20 100 000 0000" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase text-gray-700">Contact Email</label>
                <input type="email" wire:model="contact_email" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                @error('contact_email') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700">Physical Address (English)</label>
                    <input type="text" wire:model="address_en" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700">Physical Address (Arabic)</label>
                    <input type="text" wire:model="address_ar" dir="rtl" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                </div>
            </div>
        @elseif($activeTab === 'social')
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700">Instagram Profile URL</label>
                    <input type="url" wire:model="instagram_url" placeholder="https://instagram.com/sultanfabrics" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                    @error('instagram_url') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700">Facebook Page URL</label>
                    <input type="url" wire:model="facebook_url" placeholder="https://facebook.com/sultanfabrics" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                    @error('facebook_url') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase text-gray-700">TikTok Profile URL</label>
                    <input type="url" wire:model="tiktok_url" placeholder="https://tiktok.com/@sultanfabrics" class="mt-1 block w-full px-3 py-1.5 border rounded-md text-sm">
                    @error('tiktok_url') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </div>
            </div>
        @endif

        <div class="pt-4 border-t border-gray-200 text-right">
            <button type="submit" class="px-5 py-2 bg-slate-900 text-white rounded-md text-sm font-medium hover:bg-slate-800 transition">
                Save All Settings
            </button>
        </div>
    </form>
</div>
