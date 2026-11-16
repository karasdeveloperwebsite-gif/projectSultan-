<?php

namespace App\Livewire\Forms;

use App\Models\GalleryItem;
use App\Services\MediaService;
use Livewire\Form;

class GalleryItemForm extends Form
{
    public ?GalleryItem $item = null;

    public ?string $title_ar = '';
    public ?string $title_en = '';
    public ?string $description_ar = '';
    public ?string $description_en = '';
    public bool $is_active = true;
    public int $sort_order = 0;

    // Media fields
    public string $image_source = 'external'; // 'local' or 'external'
    public $image_file = null;
    public string $image_url = '';

    public function setItem(GalleryItem $item): void
    {
        $this->item = $item;
        $this->title_ar = $item->title_ar;
        $this->title_en = $item->title_en;
        $this->description_ar = $item->description_ar;
        $this->description_en = $item->description_en;
        $this->is_active = $item->is_active;
        $this->sort_order = $item->sort_order;

        $primary = $item->primaryImage;
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
            'description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'image_source' => ['required', 'in:local,external'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'image_url' => ['nullable', 'url', 'max:500'],
        ];
    }

    public function store(): GalleryItem
    {
        $this->validate();

        $item = GalleryItem::create([
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ]);

        $this->handleMedia($item);

        $this->reset();
        return $item;
    }

    public function update(): void
    {
        $this->validate();

        $this->item->update([
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ]);

        $this->handleMedia($this->item);

        $this->reset();
    }

    protected function handleMedia(GalleryItem $item): void
    {
        $mediaService = app(MediaService::class);

        if ($this->image_source === 'local' && $this->image_file) {
            $mediaService->attachLocal(
                $item,
                $this->image_file,
                isPrimary: true,
                altAr: $item->title_ar,
                altEn: $item->title_en,
                directory: 'gallery'
            );
        } elseif ($this->image_source === 'external' && !empty($this->image_url)) {
            $mediaService->attachExternal(
                $item,
                $this->image_url,
                isPrimary: true,
                altAr: $item->title_ar,
                altEn: $item->title_en
            );
        }
    }
}
