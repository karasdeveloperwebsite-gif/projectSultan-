<?php

namespace App\Livewire\Admin\Categories;

use App\Livewire\Forms\CategoryForm;
use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Categories Management')]
class CategoryIndex extends Component
{
    use WithPagination, WithFileUploads;

    public CategoryForm $form;

    public string $search = '';
    public string $filterParent = '';
    public string $sortField = 'sort_order';
    public string $sortDirection = 'asc';

    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterParent' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterParent(): void
    {
        $this->resetPage();
    }

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
        $this->authorize('create', Category::class);
        $this->form->reset();
        $this->isEditing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditModal(Category $category): void
    {
        $this->authorize('update', $category);
        $this->form->reset();
        $this->form->setCategory($category);
        $this->isEditing = true;
        $this->editingId = $category->id;
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
            $this->authorize('update', $this->form->category);
            $this->form->update();
            session()->flash('success', 'Category updated successfully.');
        } else {
            $this->authorize('create', Category::class);
            $this->form->store();
            session()->flash('success', 'Category created successfully.');
        }

        $this->showModal = false;
    }

    public function toggleActive(Category $category): void
    {
        $this->authorize('update', $category);
        $category->update(['is_active' => !$category->is_active]);
        session()->flash('success', "Category '{$category->name_en}' status updated.");
    }

    public function deleteCategory(Category $category): void
    {
        $this->authorize('delete', $category);
        if ($category->children()->count() > 0) {
            session()->flash('error', "Cannot delete '{$category->name_en}' because it contains subcategories.");
            return;
        }

        if ($category->products()->count() > 0) {
            session()->flash('error', "Cannot delete '{$category->name_en}' because it is assigned to {$category->products()->count()} products.");
            return;
        }

        $category->delete();
        session()->flash('success', 'Category deleted successfully.');
    }

    public function render()
    {
        $this->authorize('viewAny', Category::class);

        $query = Category::query()
            ->with(['parent', 'primaryImage'])
            ->withCount(['products', 'children']);

        if (!empty($this->search)) {
            $search = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', $search)
                  ->orWhere('name_ar', 'like', $search)
                  ->orWhere('slug_en', 'like', $search);
            });
        }

        if ($this->filterParent === 'root') {
            $query->whereNull('parent_id');
        } elseif (!empty($this->filterParent)) {
            $query->where('parent_id', $this->filterParent);
        }

        $categories = $query->orderBy($this->sortField, $this->sortDirection)->paginate(10);
        $parentCategories = Category::whereNull('parent_id')->orderBy('name_en')->get();

        return view('livewire.admin.categories.category-index', [
            'categories' => $categories,
            'parentCategories' => $parentCategories,
        ]);
    }
}
