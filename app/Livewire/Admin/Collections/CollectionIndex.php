<?php

namespace App\Livewire\Admin\Collections;

use App\Livewire\Forms\CollectionForm;
use App\Models\Collection;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Marketing Collections')]
class CollectionIndex extends Component
{
    use WithPagination, WithFileUploads;

    public CollectionForm $form;

    public string $search = '';
    public string $filterStatus = '';
    public string $sortField = 'sort_order';
    public string $sortDirection = 'asc';

    public bool $showModal = false;
    public bool $isEditing = false;
    public ?int $editingId = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function updatingSearch(): void { $this->resetPage(); }
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
        $this->authorize('create', Collection::class);
        $this->form->reset();
        $this->isEditing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditModal(Collection $collection): void
    {
        $this->authorize('update', $collection);
        $this->form->reset();
        $this->form->setCollection($collection);
        $this->isEditing = true;
        $this->editingId = $collection->id;
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
            $this->authorize('update', $this->form->collection);
            $this->form->update();
            session()->flash('success', 'Collection updated successfully.');
        } else {
            $this->authorize('create', Collection::class);
            $this->form->store();
            session()->flash('success', 'Collection created successfully.');
        }

        $this->showModal = false;
    }

    public function toggleFeatured(Collection $collection): void
    {
        $this->authorize('update', $collection);
        $collection->update(['is_featured' => !$collection->is_featured]);
        session()->flash('success', "Featured status toggled for '{$collection->name_en}'.");
    }

    public function deleteCollection(Collection $collection): void
    {
        $this->authorize('delete', $collection);
        $collection->delete();
        session()->flash('success', 'Collection deleted successfully.');
    }

    public function render()
    {
        $this->authorize('viewAny', Collection::class);

        $query = Collection::query()
            ->with(['primaryImage'])
            ->withCount('products');

        if (!empty($this->search)) {
            $search = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', $search)
                  ->orWhere('name_ar', 'like', $search)
                  ->orWhere('slug_en', 'like', $search);
            });
        }

        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        $collections = $query->orderBy($this->sortField, $this->sortDirection)->paginate(10);
        $availableProducts = Product::orderBy('name_en')->get();

        return view('livewire.admin.collections.collection-index', [
            'collections' => $collections,
            'availableProducts' => $availableProducts,
        ]);
    }
}
