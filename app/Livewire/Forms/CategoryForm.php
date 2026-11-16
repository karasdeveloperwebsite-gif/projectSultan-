<?php

namespace App\Livewire\Forms;

use App\Models\Category;
use App\Services\MediaService;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;

class CategoryForm extends Form
{
    public ?Category $category = null;

    public ?int $parent_id = null;
    public string $name_ar = '';
    public string $name_en = '';
    public string $slug_ar = '';
    public string $slug_en = '';
    public ?string $description_ar = '';
    public ?string $description_en = '';
    public bool $is_active = true;
    public int $sort_order = 0;

    // Media fields
    public string $image_source = 'external'; // 'local' or 'external'
    public $image_file = null;
    public string $image_url = '';

    public function setCategory(Category $category): void
    {
        $this->category = $category;
        $this->parent_id = $category->parent_id;
        $this->name_ar = $category->name_ar;
        $this->name_en = $category->name_en;
        $this->slug_ar = $category->slug_ar;
        $this->slug_en = $category->slug_en;
        $this->description_ar = $category->description_ar;
        $this->description_en = $category->description_en;
        $this->is_active = $category->is_active;
        $this->sort_order = $category->sort_order;

        $primary = $category->primaryImage;
        if ($primary) {
            $this->image_source = $primary->source;
            $this->image_url = $primary->external_url ?? '';
        }
    }

    public function rules(): array
    {
        $categoryId = $this->category?->id;

        return [
            'parent_id' => ['nullable', 'exists:categories,id', Rule::notIn([$categoryId])],
            'name_ar' => ['required', 'string', 'max:191'],
            'name_en' => ['required', 'string', 'max:191'],
            'slug_ar' => ['required', 'string', 'max:191', Rule::unique('categories', 'slug_ar')->ignore($categoryId)],
            'slug_en' => ['required', 'string', 'max:191', Rule::unique('categories', 'slug_en')->ignore($categoryId)],
            'description_ar' => ['nullable', 'string'],
            'description_en' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'sort_order' => ['integer', 'min:0'],
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
            // URL-friendly Arabic slug using slug transliteration or hyphen separation
            $cleanAr = preg_replace('/[^\p{Arabic}\p{N}\s-]/u', '', $this->name_ar);
            $this->slug_ar = preg_replace('/[\s-]+/', '-', trim($cleanAr));
        }
    }

    public function store(): Category
    {
        $this->generateSlugs();
        $this->validate();

        $category = Category::create([
            'parent_id' => $this->parent_id ?: null,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug_ar' => $this->slug_ar,
            'slug_en' => $this->slug_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ]);

        $this->handleMedia($category);

        $this->reset();
        return $category;
    }

    public function update(): void
    {
        $this->generateSlugs();
        $this->validate();

        $this->category->update([
            'parent_id' => $this->parent_id ?: null,
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'slug_ar' => $this->slug_ar,
            'slug_en' => $this->slug_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ]);

        $this->handleMedia($this->category);

        $this->reset();
    }

    protected function handleMedia(Category $category): void
    {
        $mediaService = app(MediaService::class);

        if ($this->image_source === 'local' && $this->image_file) {
            $mediaService->attachLocal(
                $category,
                $this->image_file,
                isPrimary: true,
                altAr: $category->name_ar,
                altEn: $category->name_en,
                directory: 'categories'
            );
        } elseif ($this->image_source === 'external' && !empty($this->image_url)) {
            $mediaService->attachExternal(
                $category,
                $this->image_url,
                isPrimary: true,
                altAr: $category->name_ar,
                altEn: $category->name_en
            );
        }
    }
}
