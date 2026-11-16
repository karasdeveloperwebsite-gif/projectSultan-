<?php

namespace App\Livewire\Forms;

use App\Models\Collection;
use App\Services\MediaService;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CollectionForm extends Form
{
    public ?Collection $collection = null;

    public string $name_ar = '';
    public string $name_en = '';
    public string $slug_ar = '';
    public string $slug_en = '';
    public ?string $description_ar = '';
    public ?string $description_en = '';
    public string $status = 'draft';
    public bool $is_featured = false;
    public int $sort_order = 0;
    public array $product_ids = [];

    // Media fields
    public string $image_source = 'external';
    public $image_file = null;
    public string $image_url = '';

    public function setCollection(Collection $collection): void
    {
        $this->collection = $collection;
        $this->name_ar = $collection->name_ar;
        $this->name_en = $collection->name_en;
        $this->slug_ar = $collection->slug_ar;
        $this->slug_en = $collection->slug_en;
        $this->description_ar = $collection->description_ar;
        $this->description_en = $collection->description_en;
        $this->status = $collection->status;
        $this->is_featured = $collection->is_featured;
        $this->sort_order = $collection->sort_order;
        $this->product_ids = $collection->products->pluck('id')->toArray();

        $primary = $collection->primaryImage;
        if ($primary) {
            $this->image_source = $primary->source;
            $this->image_url = $primary->external_url ?? '';
        }
    }

    public function rules(): array
    {
        $collectionId = $this->collection?->id;

        return [
            'name_ar' => ['required', 'string', 'max:191'],
            'name_en' => ['required', 'string', 'max:191'],
            'slug_ar' => ['required', 'string', 'max:191', Rule::unique('collections', 'slug_ar')->ignore($collectionId)],
            'slug_en' => ['required', 'string', 'max:191', Rule::unique('collections', 'slug_en')->ignore($collectionId)],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'status' => ['required', 'in:draft,published,archived'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'product_ids' => ['array'],
            'product_ids.*' => ['exists:products,id'],
            'image_source' => ['required', 'in:local,external'],
            'image_file' => ['nullable', 'image', 'max:4096'],
            'image_url' => ['nullable', 'url', 'max:500'],
        ];
    }

    public function generateSlugs(): void
    {
        if (empty($this->slug_en) && !empty($this->name_en)) {
            $this->slug_en = Str::slug($this->name_en);
        }
        if (empty($this->slug_ar) && !empty($this->name_ar)) {
            $cleanAr = preg_replace('/[^\p{Arabic}\p{N}\s-]/u', '', $this->name_ar);
            $this->slug_ar = preg_replace('/[\s-]+/', '-', trim($cleanAr));
        }
    }

    public function store(): Collection
    {
        $this->generateSlugs();
        $this->validate();

        $collection = Collection::create([
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug_ar' => $this->slug_ar,
            'slug_en' => $this->slug_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'published_at' => $this->status === 'published' ? now() : null,
        ]);

        if (!empty($this->product_ids)) {
            $collection->products()->sync($this->product_ids);
        }

        $this->handleMedia($collection);

        $this->reset();
        return $collection;
    }

    public function update(): void
    {
        $this->generateSlugs();
        $this->validate();

        $publishedAt = $this->collection->published_at;
        if ($this->status === 'published' && !$publishedAt) {
            $publishedAt = now();
        } elseif ($this->status !== 'published') {
            $publishedAt = null;
        }

        $this->collection->update([
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug_ar' => $this->slug_ar,
            'slug_en' => $this->slug_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'published_at' => $publishedAt,
        ]);

        $this->collection->products()->sync($this->product_ids);

        $this->handleMedia($this->collection);

        $this->reset();
    }

    protected function handleMedia(Collection $collection): void
    {
        $mediaService = app(MediaService::class);

        if ($this->image_source === 'local' && $this->image_file) {
            $mediaService->attachLocal(
                $collection,
                $this->image_file,
                isPrimary: true,
                altAr: $collection->name_ar,
                altEn: $collection->name_en,
                directory: 'collections'
            );
        } elseif ($this->image_source === 'external' && !empty($this->image_url)) {
            $mediaService->attachExternal(
                $collection,
                $this->image_url,
                isPrimary: true,
                altAr: $collection->name_ar,
                altEn: $collection->name_en
            );
        }
    }
}
