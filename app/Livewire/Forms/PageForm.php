<?php

namespace App\Livewire\Forms;

use App\Models\Page;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Form;

class PageForm extends Form
{
    public ?Page $page = null;

    public string $title_ar = '';
    public string $title_en = '';
    public string $slug_ar = '';
    public string $slug_en = '';
    public ?string $content_ar = '';
    public ?string $content_en = '';
    public ?string $meta_title_ar = '';
    public ?string $meta_title_en = '';
    public ?string $meta_description_ar = '';
    public ?string $meta_description_en = '';
    public string $status = 'published';

    public function setPage(Page $page): void
    {
        $this->page = $page;
        $this->title_ar = $page->title_ar;
        $this->title_en = $page->title_en;
        $this->slug_ar = $page->slug_ar;
        $this->slug_en = $page->slug_en;
        $this->content_ar = $page->content_ar;
        $this->content_en = $page->content_en;
        $this->meta_title_ar = $page->meta_title_ar;
        $this->meta_title_en = $page->meta_title_en;
        $this->meta_description_ar = $page->meta_description_ar;
        $this->meta_description_en = $page->meta_description_en;
        $this->status = $page->status;
    }

    public function rules(): array
    {
        $pageId = $this->page?->id;

        return [
            'title_en' => ['required', 'string', 'max:191'],
            'title_ar' => ['required', 'string', 'max:191'],
            'slug_en' => ['required', 'string', 'max:191', Rule::unique('pages', 'slug_en')->ignore($pageId)],
            'slug_ar' => ['required', 'string', 'max:191', Rule::unique('pages', 'slug_ar')->ignore($pageId)],
            'content_en' => ['nullable', 'string'],
            'content_ar' => ['nullable', 'string'],
            'meta_title_en' => ['nullable', 'string', 'max:191'],
            'meta_title_ar' => ['nullable', 'string', 'max:191'],
            'meta_description_en' => ['nullable', 'string'],
            'meta_description_ar' => ['nullable', 'string'],
            'status' => ['required', 'in:published,draft'],
        ];
    }

    public function generateSlugs(): void
    {
        if (empty($this->slug_en) && !empty($this->title_en)) {
            $this->slug_en = Str::slug($this->title_en);
        }
        if (empty($this->slug_ar) && !empty($this->title_ar)) {
            $cleanAr = preg_replace('/[^\p{Arabic}\p{N}\s-]/u', '', $this->title_ar);
            $this->slug_ar = preg_replace('/[\s-]+/', '-', trim($cleanAr));
        }
    }

    public function store(): Page
    {
        $this->generateSlugs();
        $this->validate();

        $page = Page::create([
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'slug_ar' => $this->slug_ar,
            'slug_en' => $this->slug_en,
            'content_ar' => $this->content_ar,
            'content_en' => $this->content_en,
            'meta_title_ar' => $this->meta_title_ar,
            'meta_title_en' => $this->meta_title_en,
            'meta_description_ar' => $this->meta_description_ar,
            'meta_description_en' => $this->meta_description_en,
            'status' => $this->status,
            'published_at' => $this->status === 'published' ? now() : null,
        ]);

        $this->reset();
        return $page;
    }

    public function update(): void
    {
        $this->generateSlugs();
        $this->validate();

        $publishedAt = $this->page->published_at;
        if ($this->status === 'published' && !$publishedAt) {
            $publishedAt = now();
        } elseif ($this->status !== 'published') {
            $publishedAt = null;
        }

        $this->page->update([
            'title_ar' => $this->title_ar,
            'title_en' => $this->title_en,
            'slug_ar' => $this->slug_ar,
            'slug_en' => $this->slug_en,
            'content_ar' => $this->content_ar,
            'content_en' => $this->content_en,
            'meta_title_ar' => $this->meta_title_ar,
            'meta_title_en' => $this->meta_title_en,
            'meta_description_ar' => $this->meta_description_ar,
            'meta_description_en' => $this->meta_description_en,
            'status' => $this->status,
            'published_at' => $publishedAt,
        ]);

        $this->reset();
    }
}
