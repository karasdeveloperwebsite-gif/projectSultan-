<div>
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Inbound Contact Messages</h2>
            <p class="text-xs text-gray-500">Customer requests, tailoring questions, and showroom inquiries.</p>
        </div>

        <div class="flex items-center space-x-3 rtl:space-x-reverse">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by sender or phone..."
                   class="px-3 py-1.5 border rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">

            <select wire:model.live="filterStatus" class="py-1.5 px-3 border rounded-md text-sm">
                <option value="">All Statuses</option>
                <option value="new">New (Unread)</option>
                <option value="read">Read</option>
                <option value="replied">Replied</option>
                <option value="archived">Archived</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left rtl:text-right">
                <tr>
                    <th class="px-4 py-3">Sender Name</th>
                    <th class="px-4 py-3">Phone / WhatsApp</th>
                    <th class="px-4 py-3">Subject / Message</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Received</th>
                    <th class="px-4 py-3 text-right rtl:text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($messages as $msg)
                    <tr class="hover:bg-gray-50/60 transition {{ $msg->status === 'new' ? 'bg-amber-50/30 font-medium' : '' }}">
                        <td class="px-4 py-3 text-gray-900">
                            <div>{{ $msg->name }}</div>
                            <div class="text-xs text-gray-400 font-normal">{{ $msg->email ?: 'No email' }}</div>
                        </td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-700">
                            {{ $msg->phone ?: '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-700 max-w-xs truncate">
                            <span class="font-semibold text-gray-900">{{ $msg->subject ?: 'General Inquiry' }}:</span>
                            {{ Str::limit($msg->message, 60) }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <select wire:change="updateStatus({{ $msg->id }}, $event.target.value)"
                                    class="text-xs py-1 px-2 border rounded font-semibold {{ $msg->status === 'new' ? 'bg-amber-100 text-amber-800 border-amber-300' : ($msg->status === 'replied' ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'bg-gray-100 text-gray-700 border-gray-300') }}">
                                <option value="new" {{ $msg->status === 'new' ? 'selected' : '' }}>New</option>
                                <option value="read" {{ $msg->status === 'read' ? 'selected' : '' }}>Read</option>
                                <option value="replied" {{ $msg->status === 'replied' ? 'selected' : '' }}>Replied</option>
                                <option value="archived" {{ $msg->status === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                        </td>
                        <td class="px-4 py-3 text-center text-xs text-gray-500">
                            {{ $msg->created_at->diffForHumans() }}
                        </td>
                        <td class="px-4 py-3 text-right rtl:text-left space-x-2 rtl:space-x-reverse font-medium">
                            <button wire:click="viewMessage({{ $msg->id }})" class="text-slate-600 hover:text-slate-900">View</button>
                            <button wire:click="deleteMessage({{ $msg->id }})" wire:confirm="Delete this message?" class="text-rose-600 hover:text-rose-900">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">No contact messages received.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t border-gray-200">
            {{ $messages->links() }}
        </div>
    </div>

    <!-- Message Detail Modal -->
    @if($showMessageModal && $selectedMessage)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/50 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-lg border border-gray-200 shadow-xl max-w-lg w-full p-6" @click.outside="$wire.closeModal()">
                <div class="flex items-center justify-between pb-3 border-b border-gray-200 mb-4">
                    <div>
                        <h3 class="text-base font-bold text-gray-900">{{ $selectedMessage->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $selectedMessage->created_at->format('M d, Y - h:i A') }}</p>
                    </div>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded text-xs">
                        <div>
                            <span class="text-gray-500">Phone:</span>
                            <span class="font-mono font-bold text-gray-800 ml-1">{{ $selectedMessage->phone ?: 'Not provided' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500">Email:</span>
                            <span class="font-bold text-gray-800 ml-1">{{ $selectedMessage->email ?: 'Not provided' }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase text-gray-500">Subject</div>
                        <div class="font-medium text-gray-900 mt-0.5">{{ $selectedMessage->subject ?: 'General Inquiry' }}</div>
                    </div>

                    <div>
                        <div class="text-xs font-semibold uppercase text-gray-500">Message Body</div>
                        <div class="mt-1 p-3 bg-gray-50 border rounded text-gray-800 whitespace-pre-wrap leading-relaxed">
                            {{ $selectedMessage->message }}
                        </div>
                    </div>

                    @if($selectedMessage->phone)
                        <div class="pt-2">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $selectedMessage->phone) }}" target="_blank"
                               class="inline-flex items-center justify-center w-full py-2 px-4 rounded bg-emerald-600 hover:bg-emerald-700 text-white font-medium text-xs transition">
                                Reply via WhatsApp &nearr;
                            </a>
                        </div>
                    @endif
                </div>

                <div class="mt-4 pt-3 border-t border-gray-200 text-right">
                    <button wire:click="closeModal" class="px-4 py-1.5 border rounded text-sm text-gray-700">Close</button>
                </div>
            </div>
        </div>
    @endif
</div>
