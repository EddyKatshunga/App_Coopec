<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class PermissionMatrix extends Component
{
    public $newRoleName = '';
    public $newPermissionName = '';
    
    // Pour gérer l'état des checkboxes
    // Format : ['role_id' => ['perm_name1', 'perm_name2']]
    public $matrix = [];

    protected $rules = [
        'newRoleName' => 'required|min:3|unique:roles,name',
        'newPermissionName' => 'required|min:3|unique:permissions,name',
    ];

    public function mount()
    {
        $this->loadMatrix();
    }

    public function loadMatrix()
    {
        $roles = Role::with('permissions')->get();
        $this->matrix = []; // On réinitialise

        foreach ($roles as $role) {
            // Important : On force l'ID en string pour que Livewire ne s'emmêle pas les pinceaux
            $this->matrix[(string)$role->id] = $role->permissions->pluck('name')->toArray();
        }
    }

    public function storeRole()
    {
        $this->validateOnly('newRoleName', ['newRoleName' => 'required|unique:roles,name']);
        Role::create(['name' => $this->newRoleName]);
        $this->newRoleName = '';
        $this->loadMatrix(); // Rafraîchir
        session()->flash('success', 'Rôle ajouté !');
        $this->dispatch('close-modals'); 
    }

    public function storePermission()
    {
        $this->validateOnly('newPermissionName', ['newPermissionName' => 'required|unique:permissions,name']);
        Permission::create(['name' => $this->newPermissionName]);
        $this->newPermissionName = '';
        session()->flash('success', 'Permission ajoutée !');
        $this->dispatch('close-modals');
    }

    public function save()
    {
        foreach ($this->matrix as $roleId => $permissions) {
            $role = Role::findById($roleId);
            $role->syncPermissions($permissions);
        }

        session()->flash('success', 'Matrice mise à jour avec succès !');
    }

    public function render()
    {
        return view('livewire.admin.permission-matrix', [
            'roles' => Role::all(),
            'permissions' => Permission::all(),
        ]);
    }
}
