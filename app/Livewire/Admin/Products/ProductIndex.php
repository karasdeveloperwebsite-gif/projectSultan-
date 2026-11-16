<?php

namespace App\Livewire\Admin\Products;

use App\Livewire\Forms\ProductInputForm;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Products Management')]
class ProductIndex extends Component
{
    use WithPagination, WithFileUploads;

    public ProductInputForm $form;

    public string $search = '';
    public string $filterCategory = '';
    public string $filterStatus = '';
    public string $sortField = 'id';
    public string $sortDirection = 'desc';

    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterCategory' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterCategory(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openCreateModal(): void
    {
        $this->authorize('create', Product::class);
        $this->form->reset();
        $this->isEditing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditModal(Product $product): void
    {
        $this->authorize('update', $product);
        $this->form->reset();
        $this->form->setProduct($product);
        $this->isEditing = true;
        $this->editingId = $product->id;
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->form->reset();
    }

    public function save(): void
    {
        if ($this->isEditing) {
            $this->authorize('update', $this->form->product);
            $this->form->update();
            session()->flash('success', 'Product updated successfully.');
        } else {
            $this->authorize('create', Product::class);
            $this->form->store();
            session()->flash('success', 'Product created successfully.');
        }

        $this->showModal = false;
    }

    public function toggleFeatured(Product $product): void
    {
        $this->authorize('update', $product);
        $product->update(['is_featured' => !$product->is_featured]);
        session()->flash('success', "Featured status updated for '{$product->name_en}'.");
    }

    public function updateStatus(Product $product, string $newStatus): void
    {
        $this->authorize('update', $product);
        if (!in_array($newStatus, ['draft', 'published', 'archived'])) {
            return;
        }

        $publishedAt = $newStatus === 'published' ? ($product->published_at ?: now()) : null;
        $product->update([
            'status' => $newStatus,
            'published_at' => $publishedAt,
        ]);

        session()->flash('success', "Product status changed to '{$newStatus}'.");
    }

    public function deleteProduct(Product $product): void
    {
        $this->authorize('delete', $product);
        $product->delete();
        session()->flash('success', 'Product deleted successfully.');
    }

    public function render()
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::query()
            ->with(['category', 'primaryImage', 'collections']);

        if (!empty($this->search)) {
            $search = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', $search)
                  ->orWhere('name_ar', 'like', $search)
                  ->orWhere('sku', 'like', $search);
            });
        }

        if (!empty($this->filterCategory)) {
            $query->where('category_id', $this->filterCategory);
        }

        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        $products = $query->orderBy($this->sortField, $this->sortDirection)->paginate(10);
        $categories = Category::orderBy('name_en')->get();
        $collections = Collection::orderBy('name_en')->get();

        return view('livewire.admin.products.product-index', [
            'products' => $products,
            'categories' => $categories,
            'collections' => $collections,
        ]);
    }
}
