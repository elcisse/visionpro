<?php

namespace App\Livewire\Utilisateurs;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin', ['title' => 'Utilisateurs & rôles'])]
class Manager extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $userId = null;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public array $selectedRoles = [];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->userId)],
            'password' => $this->userId ? 'nullable|string|min:8|confirmed' : 'required|string|min:8|confirmed',
            'selectedRoles' => 'array',
            'selectedRoles.*' => 'exists:roles,name',
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorize('utilisateurs.create');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('utilisateurs.update');

        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->selectedRoles = $user->roles->pluck('name')->all();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->userId ? 'utilisateurs.update' : 'utilisateurs.create');

        $data = $this->validate();

        $user = User::updateOrCreate(
            ['id' => $this->userId],
            [
                'name' => $data['name'],
                'email' => $data['email'],
                ...($data['password'] ? ['password' => Hash::make($data['password'])] : []),
            ]
        );

        $user->syncRoles($data['selectedRoles']);

        $this->showModal = false;
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        $this->authorize('utilisateurs.delete');

        if ($id === auth()->id()) {
            $this->addError('delete', 'Vous ne pouvez pas supprimer votre propre compte.');

            return;
        }

        User::findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFields();
    }

    private function resetFields(): void
    {
        $this->reset([
            'userId', 'name', 'email', 'password', 'password_confirmation', 'selectedRoles',
        ]);
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->authorize('utilisateurs.view');

        return view('livewire.utilisateurs.manager', [
            'users' => User::query()
                ->with('roles')
                ->when($this->search, fn ($query) => $query
                    ->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%"))
                ->orderBy('name')
                ->paginate(10),
            'rolesOptions' => Role::orderBy('name')->pluck('name'),
        ]);
    }
}
