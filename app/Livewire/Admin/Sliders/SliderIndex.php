<?php

namespace App\Livewire\Admin\Sliders;

use App\Livewire\Forms\SliderForm;
use App\Models\Slider;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Hero Sliders')]
class SliderIndex extends Component
{
    use WithPagination, WithFileUploads;

    public SliderForm $form;

    public ?int $editingId = null;
    public bool $showModal = false;
    public bool $isEditing = false;

    public function openCreateModal(): void
    {
        $this->authorize('create', Slider::class);
        $this->form->reset();
        $this->isEditing = false;
        $this->editingId = null;
        $this->showModal = true;
    }

    public function openEditModal(Slider $slider): void
    {
        $this->authorize('update', $slider);
        $this->form->reset();
        $this->form->setSlider($slider);
        $this->isEditing = true;
        $this->editingId = $slider->id;
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
            $this->authorize('update', $this->form->slider);
            $this->form->update();
            session()->flash('success', 'Slider updated successfully.');
        } else {
            $this->authorize('create', Slider::class);
            $this->form->store();
            session()->flash('success', 'Slider created successfully.');
        }

        $this->showModal = false;
    }

    public function toggleActive(Slider $slider): void
    {
        $this->authorize('update', $slider);
        $slider->update(['is_active' => !$slider->is_active]);
        session()->flash('success', 'Slider status updated.');
    }

    public function deleteSlider(Slider $slider): void
    {
        $this->authorize('delete', $slider);
        $slider->delete();
        session()->flash('success', 'Slider deleted successfully.');
    }

    public function render()
    {
        $this->authorize('viewAny', Slider::class);

        $sliders = Slider::with('primaryImage')->orderBy('sort_order')->paginate(10);
        return view('livewire.admin.sliders.slider-index', ['sliders' => $sliders]);
    }
}
