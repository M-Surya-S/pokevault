<?php

namespace App\Livewire;

use App\Services\CollectionService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Koleksi Saya — PokéVault')]
class CollectionList extends Component
{
    #[Url]
    public string $search = '';

    #[Url]
    public string $sortBy = 'name';

    #[Url]
    public string $sortDir = 'asc';

    // Edit form state
    public ?int $editingId = null;
    public string $editNickname = '';
    public int $editLevel = 1;
    public string $editNotes = '';

    // Delete confirmation
    public ?int $deletingId = null;
    public string $deletingName = '';

    public function updatedSearch(): void
    {
        // Auto-refresh on search change
    }

    public function toggleSort(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDir = 'asc';
        }
    }

    public function startEdit(int $id): void
    {
        $collectionService = app(CollectionService::class);
        $item = $collectionService->find($id);

        if ($item) {
            $this->editingId = $id;
            $this->editNickname = $item->nickname ?? '';
            $this->editLevel = $item->level;
            $this->editNotes = $item->notes ?? '';
        }
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editNickname' => 'nullable|string|max:30',
            'editLevel' => 'integer|min:1|max:100',
            'editNotes' => 'nullable|string|max:200',
        ]);

        try {
            $collectionService = app(CollectionService::class);
            $collectionService->update($this->editingId, [
                'nickname' => $this->editNickname ?: null,
                'level' => $this->editLevel,
                'notes' => $this->editNotes ?: null,
            ]);

            $this->editingId = null;
            $this->dispatch('notify', message: 'Koleksi berhasil diperbarui!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function confirmDelete(int $id, string $name): void
    {
        $this->deletingId = $id;
        $this->deletingName = $name;
    }

    public function cancelDelete(): void
    {
        $this->deletingId = null;
        $this->deletingName = '';
    }

    public function deleteItem(): void
    {
        try {
            $collectionService = app(CollectionService::class);
            $collectionService->remove($this->deletingId);

            $this->deletingId = null;
            $this->deletingName = '';
            $this->dispatch('notify', message: 'Pokémon berhasil dihapus dari koleksi!', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function render()
    {
        $collectionService = app(CollectionService::class);
        $items = $collectionService->all($this->search, $this->sortBy, $this->sortDir);

        return view('livewire.collection-list', [
            'items' => $items,
        ]);
    }
}
