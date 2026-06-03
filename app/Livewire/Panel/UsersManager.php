<?php

namespace App\Livewire\Panel;

use App\Models\Role;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class UsersManager extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $roleFilter = '';

    public ?int $editingUserId = null;

    public string $prenom = '';
    public string $nom = '';
    public string $email = '';
    public string $telephone = '';
    public string $pays = '';
    public string $password = '';
    public string $roleId = '';
    public bool $actif = true;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function editUser(int $userId): void
    {
        $user = User::query()->findOrFail($userId);

        $this->editingUserId = $user->id;
        $this->prenom = (string) $user->prenom;
        $this->nom = (string) $user->nom;
        $this->email = (string) $user->email;
        $this->telephone = (string) ($user->telephone ?? '');
        $this->pays = (string) ($user->pays ?? '');
        $this->roleId = (string) ($user->role_id ?? '');
        $this->actif = (bool) $user->actif;
        $this->password = '';
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingUserId',
            'prenom',
            'nom',
            'email',
            'telephone',
            'pays',
            'password',
            'roleId',
        ]);

        $this->actif = true;
        $this->resetValidation();
    }

    public function saveUser(): void
    {
        $rules = [
            'prenom' => ['required', 'string', 'max:80'],
            'nom' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:191', Rule::unique('users', 'email')->ignore($this->editingUserId)],
            'telephone' => ['nullable', 'string', 'max:20'],
            'pays' => ['nullable', 'string', 'max:80'],
            'roleId' => ['required', 'exists:roles,id'],
            'actif' => ['boolean'],
        ];

        if ($this->editingUserId) {
            $rules['password'] = ['nullable', Password::min(8)->letters()->numbers()];
        } else {
            $rules['password'] = ['required', Password::min(8)->letters()->numbers()];
        }

        $validated = $this->validate($rules);

        $payload = [
            'role_id' => (int) $validated['roleId'],
            'prenom' => $validated['prenom'],
            'nom' => $validated['nom'],
            'name' => trim($validated['prenom'] . ' ' . $validated['nom']),
            'email' => $validated['email'],
            'telephone' => $validated['telephone'] ?: null,
            'pays' => $validated['pays'] ?: null,
            'actif' => $validated['actif'],
        ];

        if (! empty($validated['password'])) {
            $payload['password'] = $validated['password'];
        }

        User::query()->updateOrCreate(
            ['id' => $this->editingUserId],
            $payload
        );

        session()->flash('panel_success', __('admin.flash.user_saved'));
        $this->resetForm();
    }

    public function toggleUser(int $userId): void
    {
        $user = User::query()->findOrFail($userId);
        $user->update(['actif' => ! $user->actif]);
    }

    public function render(): View
    {
        $search = trim($this->search);

        $users = User::query()
            ->with('role')
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('prenom', 'like', $term)
                        ->orWhere('nom', 'like', $term)
                        ->orWhere('name', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->when($this->roleFilter !== '', fn ($query) => $query->where('role_id', $this->roleFilter))
            ->latest()
            ->paginate(10);

        return view('livewire.panel.users-manager', [
            'users' => $users,
            'roles' => Role::query()->where('actif', true)->orderBy('libelle')->get(),
        ]);
    }
}
