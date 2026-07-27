<?php

namespace App\Livewire\Entreprise;

use App\Models\Entreprise;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.admin', ['title' => 'Entreprise'])]
class Settings extends Component
{
    use WithFileUploads;

    public ?int $entrepriseId = null;

    public string $nom = '';

    public string $ninea = '';

    public string $adresse = '';

    public string $telephone = '';

    public string $email = '';

    public string $site_web = '';

    public string $devise = 'FCFA';

    public $logo = null;

    public ?string $currentLogoPath = null;

    protected function rules(): array
    {
        return [
            'nom' => 'required|string|max:255',
            'ninea' => 'nullable|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'site_web' => 'nullable|string|max:255',
            'devise' => 'required|string|max:10',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,svg|max:2048',
        ];
    }

    public function mount(): void
    {
        $this->authorize('entreprise.view');

        $entreprise = Entreprise::first();

        if ($entreprise) {
            $this->entrepriseId = $entreprise->id;
            $this->nom = $entreprise->nom;
            $this->ninea = (string) $entreprise->ninea;
            $this->adresse = (string) $entreprise->adresse;
            $this->telephone = (string) $entreprise->telephone;
            $this->email = (string) $entreprise->email;
            $this->site_web = (string) $entreprise->site_web;
            $this->devise = $entreprise->devise;
            $this->currentLogoPath = $entreprise->logo;
        }
    }

    public function save(): void
    {
        $this->authorize('entreprise.update');

        $data = $this->validate();
        unset($data['logo']);

        if ($this->logo) {
            if ($this->currentLogoPath) {
                Storage::disk('public')->delete($this->currentLogoPath);
            }
            $data['logo'] = $this->logo->store('entreprise', 'public');
            $this->currentLogoPath = $data['logo'];
        }

        $entreprise = Entreprise::updateOrCreate(['id' => $this->entrepriseId], $data);
        $this->entrepriseId = $entreprise->id;
        $this->logo = null;

        session()->flash('status', 'Informations de l\'entreprise enregistrées.');
    }

    public function render()
    {
        return view('livewire.entreprise.settings');
    }
}
