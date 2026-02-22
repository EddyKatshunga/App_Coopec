<div class="container mx-auto py-8" x-data="{ roleModal: false, permissionModal: false }" 
     x-on:close-modals.window="roleModal = false; permissionModal = false">

    <div class="flex gap-4 mb-6">
        <button @click="roleModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg shadow text-sm transition">
            + Nouveau Rôle
        </button>
        <button @click="permissionModal = true" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg shadow text-sm transition">
            + Nouvelle Permission
        </button>
    </div>

    <div x-show="roleModal" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 flex items-center justify-center" x-cloak>
        <div class="bg-white rounded-lg shadow-xl w-96 p-6" @click.away="roleModal = false">
            <h2 class="text-xl font-bold mb-4">Créer un nouveau rôle</h2>
            <input type="text" wire:model="newRoleName" placeholder="Nom du rôle..." class="w-full border rounded-lg px-3 py-2 mb-2 outline-none focus:ring-2 focus:ring-indigo-500">
            @error('newRoleName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="roleModal = false" class="px-4 py-2 text-gray-500 hover:text-gray-700">Annuler</button>
                <button wire:click="storeRole" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">Enregistrer</button>
            </div>
        </div>
    </div>

    <div x-show="permissionModal" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 flex items-center justify-center" x-cloak>
        <div class="bg-white rounded-lg shadow-xl w-96 p-6" @click.away="permissionModal = false">
            <h2 class="text-xl font-bold mb-4">Créer une permission</h2>
            <input type="text" wire:model="newPermissionName" placeholder="Nom de la permission..." class="w-full border rounded-lg px-3 py-2 mb-2 outline-none focus:ring-2 focus:ring-emerald-500">
            @error('newPermissionName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            <div class="flex justify-end gap-2 mt-4">
                <button type="button" @click="permissionModal = false" class="px-4 py-2 text-gray-500 hover:text-gray-700">Annuler</button>
                <button wire:click="storePermission" class="px-4 py-2 bg-emerald-600 text-white rounded-lg">Enregistrer</button>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Matrice des Permissions</h1>
        <div class="flex items-center gap-4">
            <span wire:loading wire:target="save" class="text-sm text-gray-500 italic">Synchronisation...</span>
            <button wire:click="save" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg shadow transition font-medium">
                Enregistrer les modifications
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow-md rounded-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b">
                        <th class="p-4 font-semibold text-gray-700 border-r w-64 italic">Permissions \ Rôles</th>
                        @foreach($roles as $role)
                            <th class="p-4 font-semibold text-gray-700 text-center uppercase tracking-wider text-xs border-r last:border-r-0">
                                {{ $role->name }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $permission)
                        <tr class="border-b hover:bg-gray-50 transition">
                            <td class="p-4 text-gray-600 font-medium border-r bg-gray-50/30">
                                {{ $permission->name }}
                            </td>
                            @foreach($roles as $role)
                                <td class="p-4 text-center border-r last:border-r-0" wire:key="role-{{ $role->id }}-perm-{{ $permission->id }}">
                                    <input 
                                        type="checkbox" 
                                        wire:model="matrix.{{ $role->id }}" 
                                        value="{{ $permission->name }}"
                                        class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer"
                                    >
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>