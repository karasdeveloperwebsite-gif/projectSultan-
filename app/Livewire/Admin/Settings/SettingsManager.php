<?php

namespace App\Livewire\Admin\Settings;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Site Settings')]
class SettingsManager extends Component
{
    public string $site_name_ar = '';
    public string $site_name_en = '';
    public string $site_tagline_ar = '';
    public string $site_tagline_en = '';
    public string $whatsapp_number = '';
    public string $contact_phone = '';
    public string $contact_email = '';
    public string $address_ar = '';
    public string $address_en = '';
    public string $currency_code = 'EGP';
    public string $instagram_url = '';
    public string $facebook_url = '';
    public string $tiktok_url = '';

    public string $activeTab = 'general';

    public function mount(): void
    {
        $this->site_name_ar = (string) Setting::get('site_name', '', 'ar');
        $this->site_name_en = (string) Setting::get('site_name', '', 'en');
        $this->site_tagline_ar = (string) Setting::get('site_tagline', '', 'ar');
        $this->site_tagline_en = (string) Setting::get('site_tagline', '', 'en');
        $this->whatsapp_number = (string) Setting::get('whatsapp_number', '');
        $this->contact_phone = (string) Setting::get('contact_phone', '');
        $this->contact_email = (string) Setting::get('contact_email', '');
        $this->address_ar = (string) Setting::get('address', '', 'ar');
        $this->address_en = (string) Setting::get('address', '', 'en');
        $this->currency_code = (string) Setting::get('currency_code', 'EGP');
        $this->instagram_url = (string) Setting::get('instagram_url', '');
        $this->facebook_url = (string) Setting::get('facebook_url', '');
        $this->tiktok_url = (string) Setting::get('tiktok_url', '');
    }

    public function save(): void
    {
        $this->authorize('update', Setting::class);

        $this->validate([
            'site_name_en' => ['required', 'string', 'max:191'],
            'site_name_ar' => ['required', 'string', 'max:191'],
            'whatsapp_number' => ['required', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email'],
            'currency_code' => ['required', 'string', 'max:10'],
            'instagram_url' => ['nullable', 'url'],
            'facebook_url' => ['nullable', 'url'],
            'tiktok_url' => ['nullable', 'url'],
        ]);

        Setting::set('site_name', ['ar' => $this->site_name_ar, 'en' => $this->site_name_en], 'general', 'json');
        Setting::set('site_tagline', ['ar' => $this->site_tagline_ar, 'en' => $this->site_tagline_en], 'general', 'json');
        Setting::set('whatsapp_number', $this->whatsapp_number, 'contact', 'string');
        Setting::set('contact_phone', $this->contact_phone, 'contact', 'string');
        Setting::set('contact_email', $this->contact_email, 'contact', 'string');
        Setting::set('address', ['ar' => $this->address_ar, 'en' => $this->address_en], 'contact', 'json');
        Setting::set('currency_code', strtoupper($this->currency_code), 'general', 'string');
        Setting::set('instagram_url', $this->instagram_url, 'social', 'string');
        Setting::set('facebook_url', $this->facebook_url, 'social', 'string');
        Setting::set('tiktok_url', $this->tiktok_url, 'social', 'string');

        session()->flash('success', 'Site settings saved successfully.');
    }

    public function render()
    {
        $this->authorize('viewAny', Setting::class);

        return view('livewire.admin.settings.settings-manager');
    }
}
