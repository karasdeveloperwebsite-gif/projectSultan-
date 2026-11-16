<?php

namespace App\Livewire\Admin\Gallery;

use App\Livewire\Forms\GalleryItemForm;
use App\Models\GalleryItem;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Showroom Gallery')]
class GalleryIndex extends Component
{
    use WithPagination, WithFileUploads;

    public GalleryItemForm $form;

    public ?int $editingId = null;
    public bool $showModal = false;
    public bool $isEditing = false;

    public function openCreateModal(): void
    {
        $this->authorize('create', GalleryItem::class);
        $this->form->reset();
        $this->isEditing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditModal(GalleryItem $item): void
    {
        $this->authorize('update', $item);
        $this->form->reset();
        $this->form->setItem($item);
        $this->isEditing = true;
        $this->editingId = $item->id;
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
            $this->authorize('update', $this->form->item);
            $this->form->update();
            session()->flash('success', 'Gallery item updated successfully.');
        } else {
            $this->authorize('create', GalleryItem::class);
            $this->form->store();
            session()->flash('success', 'Gallery item added successfully.');
        }

        $this->showModal = false;
    }

    public function toggleActive(GalleryItem $item): void
    {
        $this->authorize('update', $item);
        $item->update(['is_active' => !$item->is_active]);
        session()->flash('success', 'Item status updated.');
    }

    public function deleteItem(GalleryItem $item): void
    {
        $this->authorize('delete', $item);
        $item->delete();
        session()->flash('success', 'Gallery item deleted successfully.');
    }

    public function render()
    {
        $this->authorize('viewAny', GalleryItem::class);

        $items = GalleryItem::with('primaryImage')->orderBy('sort_order')->paginate(12);
        return view('livewire.admin.gallery.gallery-index', ['items' => $items]);
    }
}
