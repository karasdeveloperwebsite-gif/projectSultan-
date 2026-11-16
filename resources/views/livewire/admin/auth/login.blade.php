<div class="bg-white p-8 rounded-lg shadow-sm border border-gray-200">
    <div class="text-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Sultan Fabrics</h2>
        <p class="text-sm text-gray-500 mt-1">Admin Control Center</p>
    </div>

    <form wire:submit="authenticate" class="space-y-4">
        <div>
            <label for="email" class="block text-xs font-semibold uppercase text-gray-700 tracking-wider">Email Address</label>
            <input type="email" id="email" wire:model="email" autofocus
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
            @error('email') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold uppercase text-gray-700 tracking-wider">Password</label>
            <input type="password" id="password" wire:model="password"
                   class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-slate-900">
            @error('password') <span class="text-xs text-rose-600 mt-1 block">{{ $message }}</span> @enderror
        </div>

        <div class="flex items-center justify-between text-xs text-gray-600">
            <label class="flex items-center">
                <input type="checkbox" wire:model="remember" class="rounded border-gray-300 text-slate-900 focus:ring-slate-900">
                <span class="ml-2 rtl:mr-2">Remember me</span>
            </label>
        </div>

        <button type="submit" wire:loading.attr="disabled"
                class="w-full py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 disabled:opacity-50 transition">
            <span wire:loading.remove>Sign In</span>
            <span wire:loading>Signing in...</span>
        </button>
    </form>
</div>
