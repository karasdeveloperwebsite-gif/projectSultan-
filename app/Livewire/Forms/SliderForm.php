<?php

namespace App\Livewire\Forms;

use App\Models\Slider;
use App\Services\MediaService;
use Livewire\Form;

class SliderForm extends Form
{
    public ?Slider $slider = null;

    public ?string $title_ar = '';
    public ?string $title_en = '';
    public ?string $subtitle_ar = '';
    public ?string $subtitle_en = '';
    public ?string $button_text_ar = '';
    public ?string $button_text_en = '';
    public ?string $button_url = '';
    public bool $is_active = true;
    public int $sort_order = 0;

    // Media fields
    public string $image_source = 'external'; // 'local' or 'external'
    public $image_file = null;
    public string $image_url = '';

    public function setSlider(Slider $slider): void
    {
        $this->slider = $slider;
        $this->title_ar = $slider->title_ar;
        $this->title_en = $slider->title_en;
        $this->subtitle_ar = $slider->subtitle_ar;
        $this->subtitle_en = $slider->subtitle_en;
        $this->button_text_ar = $slider->button_text_ar;
        $this->button_text_en = $slider->button_text_en;
        $this->button_url = $slider->button_url;
        $this->is_active = $slider->is_active;
        $this->sort_order = $slider->sort_order;

        $primary = $slider->primaryImage;
        if ($primary) {
            $this->image_source = $primary->source;
            $this->image_url = $primary->external_url ?? '';
        }
    }

    public function rules(): array
    {
        return [
            'title_en' => ['nullable', 'string', 'max:191'],
            'title_ar' => ['nullable', 'string', 'max:191'],
            'subtitle_en' => ['nullable', 'string', 'max:191'],
            'subtitle_ar' => ['nullable', 'string', 'max:191'],
            'button_text_en' => ['nullable', 'string', 'max:100'],
            'button_text_ar' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:500'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'image_source' => ['required', 'in:local,external'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'image_url' => ['nullable', 'url', 'max:500'],
        ];
    }

    public function store(): Slider
    {
        $this->validate();

        $slider = Slider::create([
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'subtitle_ar' => $this->subtitle_ar,
            'subtitle_en' => $this->subtitle_en,
            'button_text_ar' => $this->button_text_ar,
            'button_text_en' => $this->button_text_en,
            'button_url' => $this->button_url,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ]);

        $this->handleMedia($slider);

        $this->reset();
        return $slider;
    }

    public function update(): void
    {
        $this->validate();

        $this->slider->update([
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'subtitle_ar' => $this->subtitle_ar,
            'subtitle_en' => $this->subtitle_en,
            'button_text_ar' => $this->button_text_ar,
            'button_text_en' => $this->button_text_en,
            'button_url' => $this->button_url,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ]);

        $this->handleMedia($this->slider);

        $this->reset();
    }

    protected function handleMedia(Slider $slider): void
    {
        $mediaService = app(MediaService::class);

        if ($this->image_source === 'local' && $this->image_file) {
            $mediaService->attachLocal(
                $slider,
                $this->image_file,
                isPrimary: true,
                altAr: $slider->title_ar,
                altEn: $slider->title_en,
                directory: 'sliders'
            );
        } elseif ($this->image_source === 'external' && !empty($this->image_url)) {
            $mediaService->attachExternal(
                $slider,
                $this->image_url,
                isPrimary: true,
                altAr: $slider->title_ar,
                altEn: $slider->title_en
            );
        }
    }
}
