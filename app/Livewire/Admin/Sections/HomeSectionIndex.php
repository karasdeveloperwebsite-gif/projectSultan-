<?php

namespace App\Livewire\Admin\Sections;

use App\Models\HomeSection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Homepage Sections')]
class HomeSectionIndex extends Component
{
    public function toggleActive(HomeSection $section): void
    {
        $this->authorize('update', $section);
        $section->update(['is_active' => !$section->is_active]);
        session()->flash('success', "Section '{$section->section_key}' status updated.");
    }

    public function moveUp(HomeSection $section): void
    {
        $this->authorize('update', $section);
        $previous = HomeSection::where('sort_order', '<', $section->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($previous) {
            $prevOrder = $previous->sort_order;
            $previous->update(['sort_order' => $section->sort_order]);
            $section->update(['sort_order' => $prevOrder]);
        }
    }

    public function moveDown(HomeSection $section): void
    {
        $this->authorize('update', $section);
        $next = HomeSection::where('sort_order', '>', $section->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($next) {
            $nextOrder = $next->sort_order;
            $next->update(['sort_order' => $section->sort_order]);
            $section->update(['sort_order' => $nextOrder]);
        }
    }

    public function render()
    {
        $this->authorize('viewAny', HomeSection::class);

        $sections = HomeSection::orderBy('sort_order')->get();
        return view('livewire.admin.sections.home-section-index', ['sections' => $sections]);
    }
}
