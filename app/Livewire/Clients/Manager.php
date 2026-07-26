<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin', ['title' => 'Gestion des clients'])]
class Manager extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showModal = false;

    public ?int $clientId = null;

    public string $type_client = 'entreprise';

    public string $nom = '';

    public string $telephone = '';

    public string $email = '';

    public string $adresse = '';

    public string $ninea = '';

    public string $statut = 'actif';

    public array $typesClient = [
        'particulier' => 'Particulier',
        'entreprise' => 'Entreprise',
    ];

    public array $statuts = [
        'actif' => 'Actif',
        'inactif' => 'Inactif',
    ];

    protected function rules(): array
    {
        return [
            'type_client' => 'required|in:'.implode(',', array_keys($this->typesClient)),
            'nom' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'adresse' => 'nullable|string|max:255',
            'ninea' => 'nullable|string|max:255',
            'statut' => 'required|in:'.implode(',', array_keys($this->statuts)),
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function create(): void
    {
        $this->authorize('clients.create');
        $this->resetFields();
        $this->showModal = true;
    }

    public function edit(int $id): void
    {
        $this->authorize('clients.update');

        $client = Client::findOrFail($id);
        $this->clientId = $client->id;
        $this->type_client = $client->type_client;
        $this->nom = $client->nom;
        $this->telephone = (string) $client->telephone;
        $this->email = (string) $client->email;
        $this->adresse = (string) $client->adresse;
        $this->ninea = (string) $client->ninea;
        $this->statut = $client->statut;
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->authorize($this->clientId ? 'clients.update' : 'clients.create');

        $data = $this->validate();

        Client::updateOrCreate(['id' => $this->clientId], $data);

        $this->showModal = false;
        $this->resetFields();
    }

    public function delete(int $id): void
    {
        $this->authorize('clients.delete');

        Client::findOrFail($id)->delete();
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFields();
    }

    private function resetFields(): void
    {
        $this->reset([
            'clientId', 'nom', 'telephone', 'email', 'adresse', 'ninea',
        ]);
        $this->type_client = 'entreprise';
        $this->statut = 'actif';
        $this->resetErrorBag();
    }

    public function render()
    {
        $this->authorize('clients.view');

        return view('livewire.clients.manager', [
            'clients' => Client::query()
                ->when($this->search, fn ($query) => $query
                    ->where('nom', 'like', "%{$this->search}%")
                    ->orWhere('telephone', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%"))
                ->orderBy('nom')
                ->paginate(10),
        ]);
    }
}
