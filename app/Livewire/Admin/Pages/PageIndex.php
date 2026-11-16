<?php

namespace App\Livewire\Admin\Pages;

use App\Livewire\Forms\PageForm;
use App\Models\Page;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('CMS Pages')]
class PageIndex extends Component
{
    use WithPagination;

    public PageForm $form;

    public ?int $editingId = null;
    public bool $showModal = false;
    public bool $isEditing = false;

    public function openCreateModal(): void
    {
        $this->authorize('create', Page::class);
        $this->form->reset();
        $this->isEditing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditModal(Page $page): void
    {
        $this->authorize('update', $page);
        $this->form->reset();
        $this->form->setPage($page);
        $this->isEditing = true;
        $this->editingId = $page->id;
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
            $this->authorize('update', $this->form->page);
            $this->form->update();
            session()->flash('success', 'Page updated successfully.');
        } else {
            $this->authorize('create', Page::class);
            $this->form->store();
            session()->flash('success', 'Page created successfully.');
        }

        $this->showModal = false;
    }

    public function deletePage(Page $page): void
    {
        $this->authorize('delete', $page);
        $page->delete();
        session()->flash('success', 'Page deleted successfully.');
    }

    public function render()
    {
        $this->authorize('viewAny', Page::class);

        $pages = Page::orderBy('id', 'desc')->paginate(10);
        return view('livewire.admin.pages.page-index', ['pages' => $pages]);
    }
}
