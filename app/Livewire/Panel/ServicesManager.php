<?php

namespace App\Livewire\Panel;

use App\Models\Category;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ServicesManager extends Component
{
    use WithFileUploads;
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $typeFilter = '';

    #[Url(except: '')]
    public string $activeFilter = '';

    #[Url(except: '')]
    public string $categoryFilter = '';

    public ?int $editingServiceId = null;

    public string $categorieId = '';
    public string $titreFr = '';
    public string $titreEn = '';
    public string $descriptionCourteFr = '';
    public string $descriptionCourteEn = '';
    public string $descriptionLongueFr = '';
    public string $descriptionLongueEn = '';
    public string $icone = '';
    public string $type = 'autre';
    public string $prix = '';
    public string $devise = 'XOF';
    public string $dureeFr = '';
    public string $dureeEn = '';
    public string $whatsappMessageFr = '';
    public string $whatsappMessageEn = '';
    public string $ordre = '0';
    public bool $actif = true;
    public bool $enVedette = false;

    public ?TemporaryUploadedFile $image = null;

    public string $existingImagePath = '';
    public string $currentImageUrl = '';
    public bool $removeCurrentImage = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingActiveFilter(): void
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedImage(): void
    {
        $this->removeCurrentImage = false;
    }

    public function editService(int $serviceId): void
    {
        $service = Service::query()->findOrFail($serviceId);

        $this->editingServiceId = $service->id;
        $this->categorieId = (string) ($service->categorie_id ?? '');
        $this->titreFr = (string) ($service->getRawOriginal('titre_fr') ?? $service->titre);
        $this->titreEn = (string) ($service->getRawOriginal('titre_en') ?? $service->titre);
        $this->descriptionCourteFr = (string) ($service->getRawOriginal('description_courte_fr') ?? $service->description_courte);
        $this->descriptionCourteEn = (string) ($service->getRawOriginal('description_courte_en') ?? $service->description_courte);
        $this->descriptionLongueFr = (string) ($service->getRawOriginal('description_longue_fr') ?? $service->description_longue ?? '');
        $this->descriptionLongueEn = (string) ($service->getRawOriginal('description_longue_en') ?? $service->description_longue ?? '');
        $this->icone = (string) ($service->icone ?? '');
        $this->type = (string) $service->type;
        $this->prix = $service->prix !== null ? (string) $service->prix : '';
        $this->devise = (string) ($service->devise ?: 'XOF');
        $this->dureeFr = (string) ($service->getRawOriginal('duree_fr') ?? $service->duree ?? '');
        $this->dureeEn = (string) ($service->getRawOriginal('duree_en') ?? $service->duree ?? '');
        $this->whatsappMessageFr = (string) ($service->getRawOriginal('whatsapp_message_fr') ?? $service->whatsapp_message ?? '');
        $this->whatsappMessageEn = (string) ($service->getRawOriginal('whatsapp_message_en') ?? $service->whatsapp_message ?? '');
        $this->ordre = (string) ($service->ordre ?? 0);
        $this->actif = (bool) $service->actif;
        $this->enVedette = (bool) $service->en_vedette;
        $this->image = null;
        $this->existingImagePath = (string) ($service->image ?? '');
        $this->currentImageUrl = $service->publicImageUrl() ?? '';
        $this->removeCurrentImage = false;
    }

    public function resetForm(): void
    {
        $this->reset([
            'editingServiceId',
            'categorieId',
            'titreFr',
            'titreEn',
            'descriptionCourteFr',
            'descriptionCourteEn',
            'descriptionLongueFr',
            'descriptionLongueEn',
            'icone',
            'prix',
            'dureeFr',
            'dureeEn',
            'whatsappMessageFr',
            'whatsappMessageEn',
            'image',
            'existingImagePath',
            'currentImageUrl',
        ]);

        $this->type = 'autre';
        $this->devise = 'XOF';
        $this->ordre = '0';
        $this->actif = true;
        $this->enVedette = false;
        $this->removeCurrentImage = false;
        $this->resetValidation();
    }

    public function removeImage(): void
    {
        $this->image = null;
        $this->currentImageUrl = '';

        if ($this->existingImagePath !== '') {
            $this->removeCurrentImage = true;
        }
    }

    public function saveService(): void
    {
        $validated = $this->validate($this->rules());
        $service = $this->editingServiceId
            ? Service::query()->findOrFail($this->editingServiceId)
            : new Service();

        $slug = $service->exists ? $service->slug : $this->uniqueSlug(Str::slug($validated['titreFr']));

        $imagePath = $service->image;

        if ($this->removeCurrentImage && $imagePath) {
            $this->cleanupStoredImage($imagePath);
            $imagePath = null;
        }

        if ($this->image) {
            if ($imagePath) {
                $this->cleanupStoredImage($imagePath);
            }

            $imagePath = $this->image->store('services', 'public');
        }

        $service->fill([
            'categorie_id' => $validated['categorieId'] !== '' ? (int) $validated['categorieId'] : null,
            'titre' => $validated['titreFr'],
            'titre_fr' => $validated['titreFr'],
            'titre_en' => $validated['titreEn'] !== '' ? $validated['titreEn'] : $validated['titreFr'],
            'slug' => $slug !== '' ? $slug : $this->uniqueSlug('service'),
            'description_courte' => $validated['descriptionCourteFr'],
            'description_courte_fr' => $validated['descriptionCourteFr'],
            'description_courte_en' => $validated['descriptionCourteEn'] !== '' ? $validated['descriptionCourteEn'] : $validated['descriptionCourteFr'],
            'description_longue' => $validated['descriptionLongueFr'] ?: null,
            'description_longue_fr' => $validated['descriptionLongueFr'] ?: null,
            'description_longue_en' => $validated['descriptionLongueEn'] !== '' ? $validated['descriptionLongueEn'] : ($validated['descriptionLongueFr'] ?: null),
            'icone' => $validated['icone'] ?: null,
            'image' => $imagePath,
            'type' => $validated['type'],
            'prix' => $validated['prix'] !== '' ? $validated['prix'] : null,
            'devise' => $validated['devise'],
            'duree' => $validated['dureeFr'] ?: null,
            'duree_fr' => $validated['dureeFr'] ?: null,
            'duree_en' => $validated['dureeEn'] !== '' ? $validated['dureeEn'] : ($validated['dureeFr'] ?: null),
            'whatsapp_message' => $validated['whatsappMessageFr'] ?: null,
            'whatsapp_message_fr' => $validated['whatsappMessageFr'] ?: null,
            'whatsapp_message_en' => $validated['whatsappMessageEn'] !== '' ? $validated['whatsappMessageEn'] : ($validated['whatsappMessageFr'] ?: null),
            'actif' => $validated['actif'],
            'en_vedette' => $validated['enVedette'],
            'ordre' => $validated['ordre'] !== '' ? (int) $validated['ordre'] : 0,
        ]);

        $service->save();

        session()->flash('panel_success', __('admin.flash.service_saved'));
        $this->resetForm();
    }

    public function deleteService(int $serviceId): void
    {
        $service = Service::query()->findOrFail($serviceId);

        if ($service->image) {
            $this->cleanupStoredImage($service->image);
        }

        $service->delete();

        if ($this->editingServiceId === $serviceId) {
            $this->resetForm();
        }

        session()->flash('panel_success', __('admin.flash.service_deleted'));
    }

    public function render(): View
    {
        $search = trim($this->search);

        $services = Service::query()
            ->with('category')
            ->when($search !== '', function ($query) use ($search) {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term) {
                    $nested
                        ->where('titre', 'like', $term)
                        ->orWhere('titre_fr', 'like', $term)
                        ->orWhere('titre_en', 'like', $term)
                        ->orWhere('description_courte', 'like', $term)
                        ->orWhere('description_courte_fr', 'like', $term)
                        ->orWhere('description_courte_en', 'like', $term);
                });
            })
            ->when($this->typeFilter !== '', fn ($query) => $query->where('type', $this->typeFilter))
            ->when($this->activeFilter !== '', fn ($query) => $query->where('actif', $this->activeFilter === '1'))
            ->when($this->categoryFilter !== '', fn ($query) => $query->whereHas(
                'category',
                fn ($categoryQuery) => $categoryQuery->where('slug', $this->categoryFilter)
            ))
            ->orderByDesc('en_vedette')
            ->orderBy('ordre')
            ->latest('updated_at')
            ->paginate(10);

        return view('livewire.panel.services-manager', [
            'services' => $services,
            'categories' => Category::query()->where('type', 'service')->where('actif', true)->orderBy('ordre')->get(),
            'serviceTypes' => ['redaction_cv', 'coaching', 'orientation', 'accompagnement', 'autre'],
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        return [
            'categorieId' => [
                'nullable',
                Rule::exists('categories', 'id')->where(fn ($query) => $query->where('type', 'service')),
            ],
            'titreFr' => ['required', 'string', 'max:150'],
            'titreEn' => ['nullable', 'string', 'max:150'],
            'descriptionCourteFr' => ['required', 'string'],
            'descriptionCourteEn' => ['nullable', 'string'],
            'descriptionLongueFr' => ['nullable', 'string'],
            'descriptionLongueEn' => ['nullable', 'string'],
            'icone' => ['nullable', 'string', 'max:80'],
            'image' => ['nullable', 'image', 'max:4096'],
            'type' => ['required', Rule::in(['redaction_cv', 'coaching', 'orientation', 'accompagnement', 'autre'])],
            'prix' => ['nullable', 'numeric', 'min:0'],
            'devise' => ['required', 'string', 'max:10'],
            'dureeFr' => ['nullable', 'string', 'max:80'],
            'dureeEn' => ['nullable', 'string', 'max:80'],
            'whatsappMessageFr' => ['nullable', 'string', 'max:255'],
            'whatsappMessageEn' => ['nullable', 'string', 'max:255'],
            'actif' => ['boolean'],
            'enVedette' => ['boolean'],
            'ordre' => ['nullable', 'integer', 'min:0'],
        ];
    }

    protected function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug !== '' ? $baseSlug : 'service';
        $original = $slug;
        $counter = 1;

        while (Service::query()->where('slug', $slug)->exists()) {
            $slug = $original . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    protected function cleanupStoredImage(string $path): void
    {
        if ($path === '' || preg_match('/^https?:\/\//i', $path) || str_starts_with($path, 'images/')) {
            return;
        }

        $normalizedPath = str_starts_with($path, 'storage/')
            ? Str::after($path, 'storage/')
            : $path;

        Storage::disk('public')->delete($normalizedPath);
    }
}
