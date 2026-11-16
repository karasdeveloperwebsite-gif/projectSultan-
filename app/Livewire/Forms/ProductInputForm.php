<?php

namespace App\Livewire\Forms;

use App\Models\Product;
use App\Services\MediaService;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;

class ProductInputForm extends Form
{
    public ?Product $product = null;

    public ?int $category_id = null;
    public string $name_ar = '';
    public string $name_en = '';
    public string $slug_ar = '';
    public string $slug_en = '';
    public ?string $sku = '';
    public ?string $short_description_ar = '';
    public ?string $short_description_en = '';
    public ?string $description_ar = '';
    public ?string $description_en = '';
    public ?float $price = null;
    public string $status = 'draft';
    public bool $is_featured = false;
    public int $sort_order = 0;
    public array $collection_ids = [];

    // Media attachment fields
    public string $image_source = 'external'; // 'local' or 'external'
    public $image_file = null;
    public string $image_url = '';

    public function setProduct(Product $product): void
    {
        $this->product = $product;
        $this->category_id = $product->category_id;
        $this->name_ar = $product->name_ar;
        $this->name_en = $product->name_en;
        $this->slug_ar = $product->slug_ar;
        $this->slug_en = $product->slug_en;
        $this->sku = $product->sku;
        $this->short_description_ar = $product->short_description_ar;
        $this->short_description_en = $product->short_description_en;
        $this->description_ar = $product->description_ar;
        $this->description_en = $product->description_en;
        $this->price = $product->price !== null ? (float) $product->price : null;
        $this->status = $product->status;
        $this->is_featured = $product->is_featured;
        $this->sort_order = $product->sort_order;
        $this->collection_ids = $product->collections->pluck('id')->toArray();

        $primary = $product->primaryImage;
        if ($primary) {
            $this->image_source = $primary->source;
            $this->image_url = $primary->external_url ?? '';
        }
    }

    public function rules(): array
    {
        $productId = $this->product?->id;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'name_ar' => ['required', 'string', 'max:191'],
            'name_en' => ['required', 'string', 'max:191'],
            'slug_ar' => ['required', 'string', 'max:191', Rule::unique('products', 'slug_ar')->ignore($productId)],
            'slug_en' => ['required', 'string', 'max:191', Rule::unique('products', 'slug_en')->ignore($productId)],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($productId)],
            'short_description_ar' => ['nullable', 'string'],
            'short_description_en' => ['nullable', 'string'],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:draft,published,archived'],
            'is_featured' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
            'collection_ids' => ['array'],
            'collection_ids.*' => ['exists:collections,id'],
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

    public function store(): Product
    {
        $this->generateSlugs();
        $this->validate();

        $product = Product::create([
            'category_id' => $this->category_id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug_ar' => $this->slug_ar,
            'slug_en' => $this->slug_en,
            'sku' => $this->sku ?: null,
            'short_description_ar' => $this->short_description_ar,
            'short_description_en' => $this->short_description_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'price' => $this->price,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'published_at' => $this->status === 'published' ? now() : null,
        ]);

        if (!empty($this->collection_ids)) {
            $product->collections()->sync($this->collection_ids);
        }

        $this->handleMedia($product);

        $this->reset();
        return $product;
    }

    public function update(): void
    {
        $this->generateSlugs();
        $this->validate();

        $publishedAt = $this->product->published_at;
        if ($this->status === 'published' && !$publishedAt) {
            $publishedAt = now();
        } elseif ($this->status !== 'published') {
            $publishedAt = null;
        }

        $this->product->update([
            'category_id' => $this->category_id,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug_ar' => $this->slug_ar,
            'slug_en' => $this->slug_en,
            'sku' => $this->sku ?: null,
            'short_description_ar' => $this->short_description_ar,
            'short_description_en' => $this->short_description_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'price' => $this->price,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'published_at' => $publishedAt,
        ]);

        $this->product->collections()->sync($this->collection_ids);

        $this->handleMedia($this->product);

        $this->reset();
    }

    protected function handleMedia(Product $product): void
    {
        $mediaService = app(MediaService::class);

        if ($this->image_source === 'local' && $this->image_file) {
            $mediaService->attachLocal(
                $product,
                $this->image_file,
                isPrimary: true,
                altAr: $product->name_ar,
                altEn: $product->name_en,
                directory: 'products'
            );
        } elseif ($this->image_source === 'external' && !empty($this->image_url)) {
            $mediaService->attachExternal(
                $product,
                $this->image_url,
                isPrimary: true,
                altAr: $product->name_ar,
                altEn: $product->name_en
            );
        }
    }
}
