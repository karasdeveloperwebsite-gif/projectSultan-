<?php

namespace App\Livewire\Admin\Inquiries;

use App\Models\ContactMessage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Customer Inquiries')]
class ContactMessageIndex extends Component
{
    use WithPagination;

    public string $filterStatus = '';
    public string $search = '';

    public ?ContactMessage $selectedMessage = null;
    public bool $showMessageModal = false;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function viewMessage(ContactMessage $message): void
    {
        $this->authorize('view', $message);
        $this->selectedMessage = $message;
        if ($message->status === 'new') {
            $message->markAsRead();
        }
        $this->showMessageModal = true;
    }

    public function closeModal(): void
    {
        $this->showMessageModal = false;
        $this->selectedMessage = null;
    }

    public function updateStatus(ContactMessage $message, string $status): void
    {
        $this->authorize('update', $message);
        $message->update(['status' => $status]);
        session()->flash('success', 'Message status updated.');
    }

    public function deleteMessage(ContactMessage $message): void
    {
        $this->authorize('delete', $message);
        $message->delete();
        if ($this->selectedMessage?->id === $message->id) {
            $this->closeModal();
        }
        session()->flash('success', 'Message deleted.');
    }

    public function render()
    {
        $this->authorize('viewAny', ContactMessage::class);

        $query = ContactMessage::query();

        if (!empty($this->search)) {
            $s = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', $s)
                  ->orWhere('email', 'like', $s)
                  ->orWhere('phone', 'like', $s)
                  ->orWhere('subject', 'like', $s);
            });
        }

        if (!empty($this->filterStatus)) {
            $query->where('status', $this->filterStatus);
        }

        $messages = $query->latest()->paginate(10);

        return view('livewire.admin.inquiries.contact-message-index', ['messages' => $messages]);
    }
}
