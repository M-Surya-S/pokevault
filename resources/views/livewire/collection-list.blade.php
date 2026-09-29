<div>
    {{-- Toast Notification --}}
    <div
        x-data="{ show: false, message: '', type: 'success' }"
        x-on:notify.window="show = true; message = $event.detail.message; type = $event.detail.type; setTimeout(() => show = false, 4000)"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="toast toast-top toast-end z-[100]"
        style="display: none;"
    >
        <div class="alert" :class="type === 'success' ? 'alert-success' : 'alert-error'">
            <span x-text="message"></span>
        </div>
    </div>

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="text-3xl font-extrabold bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">
                Koleksi Saya
            </h1>
            <p class="text-base-content/60 mt-1">{{ $items->count() }} Pokémon tersimpan</p>
        </div>

        <div class="flex gap-2 w-full sm:w-auto">
            {{-- Search --}}
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Cari koleksi..."
                class="input input-bordered input-sm flex-1 sm:w-64"
                id="search-collection"
            />
        </div>
    </div>

    {{-- Sort Buttons --}}
    <div class="flex gap-2 mb-4">
        <button wire:click="toggleSort('name')" class="btn btn-xs {{ $sortBy === 'name' ? 'btn-primary' : 'btn-ghost' }}">
            Nama {!! $sortBy === 'name' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
        </button>
        <button wire:click="toggleSort('level')" class="btn btn-xs {{ $sortBy === 'level' ? 'btn-primary' : 'btn-ghost' }}">
            Level {!! $sortBy === 'level' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
        </button>
        <button wire:click="toggleSort('created_at')" class="btn btn-xs {{ $sortBy === 'created_at' ? 'btn-primary' : 'btn-ghost' }}">
            Tanggal {!! $sortBy === 'created_at' ? ($sortDir === 'asc' ? '↑' : '↓') : '' !!}
        </button>
    </div>

    {{-- Loading --}}
    <div wire:loading class="flex justify-center py-12">
        <span class="loading loading-spinner loading-lg text-primary"></span>
    </div>

    {{-- Collection Grid --}}
    <div wire:loading.remove>
        @if($items->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($items as $item)
                    <div class="card bg-base-100 shadow-md hover:shadow-xl transition-all duration-300 {{ $editingId === $item->id ? 'ring-2 ring-primary' : '' }}">
                        <div class="card-body p-4">
                            @if($editingId === $item->id)
                                {{-- Edit Mode --}}
                                <div class="flex items-start gap-3">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-16 h-16 object-contain" />
                                    <div class="flex-1">
                                        <h3 class="font-bold capitalize">{{ $item->name }}</h3>
                                        <span class="text-xs text-base-content/50">#{{ str_pad($item->pokemon_id, 3, '0', STR_PAD_LEFT) }}</span>
                                    </div>
                                </div>

                                <div class="form-control gap-2 mt-3">
                                    <div>
                                        <label class="label py-1"><span class="label-text text-xs">Nickname</span></label>
                                        <input type="text" wire:model="editNickname" class="input input-bordered input-sm w-full" maxlength="30" placeholder="Nickname..." />
                                        @error('editNickname') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="label py-1"><span class="label-text text-xs">Level</span></label>
                                        <input type="number" wire:model="editLevel" min="1" max="100" class="input input-bordered input-sm w-full" />
                                        @error('editLevel') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="label py-1"><span class="label-text text-xs">Catatan</span></label>
                                        <textarea wire:model="editNotes" class="textarea textarea-bordered textarea-sm w-full" maxlength="200" rows="2" placeholder="Catatan..."></textarea>
                                        @error('editNotes') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="flex gap-2 mt-1">
                                        <button wire:click="saveEdit" class="btn btn-primary btn-sm flex-1">
                                            <span wire:loading.remove wire:target="saveEdit">Simpan</span>
                                            <span wire:loading wire:target="saveEdit" class="loading loading-spinner loading-sm"></span>
                                        </button>
                                        <button wire:click="cancelEdit" class="btn btn-ghost btn-sm">Batal</button>
                                    </div>
                                </div>
                            @else
                                {{-- View Mode --}}
                                <div class="flex items-start gap-3">
                                    <a href="{{ route('pokemon.detail', $item->name) }}" wire:navigate class="shrink-0">
                                        <img
                                            src="{{ $item->image_url }}"
                                            alt="{{ $item->name }}"
                                            class="w-16 h-16 object-contain hover:scale-110 transition-transform"
                                        />
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('pokemon.detail', $item->name) }}" wire:navigate class="font-bold capitalize hover:text-primary transition-colors truncate">
                                                {{ $item->nickname ?? $item->name }}
                                            </a>
                                        </div>
                                        @if($item->nickname)
                                            <span class="text-xs text-base-content/50 capitalize">{{ $item->name }}</span>
                                        @endif
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="badge badge-sm badge-ghost">Lv. {{ $item->level }}</span>
                                            @foreach($item->types as $type)
                                                <span class="badge badge-sm badge-outline capitalize">{{ $type }}</span>
                                            @endforeach
                                        </div>
                                        @if($item->notes)
                                            <p class="text-xs text-base-content/60 mt-2 line-clamp-2">{{ $item->notes }}</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="card-actions justify-end mt-2">
                                    <button wire:click="startEdit({{ $item->id }})" class="btn btn-ghost btn-xs gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                        Edit
                                    </button>
                                    <button wire:click="confirmDelete({{ $item->id }}, '{{ addslashes($item->nickname ?? $item->name) }}')" class="btn btn-ghost btn-xs text-error gap-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Hapus
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="flex flex-col items-center justify-center py-20 text-base-content/50">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-20 w-20 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
                @if($search)
                    <p class="text-lg font-medium">Tidak ditemukan</p>
                    <p class="text-sm">Tidak ada Pokémon yang cocok dengan "{{ $search }}"</p>
                @else
                    <p class="text-lg font-medium">Koleksi masih kosong</p>
                    <p class="text-sm mb-4">Mulai jelajahi dan tambahkan Pokémon favoritmu!</p>
                    <a href="/" class="btn btn-primary" wire:navigate>Jelajahi Pokémon</a>
                @endif
            </div>
        @endif
    </div>

    {{-- Delete Confirmation Modal --}}
    @if($deletingId)
        <div class="modal modal-open" x-data x-on:keydown.escape.window="$wire.cancelDelete()">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Konfirmasi Hapus</h3>
                <p class="py-4">Apakah kamu yakin ingin menghapus <strong class="capitalize">{{ $deletingName }}</strong> dari koleksi?</p>
                <div class="modal-action">
                    <button wire:click="cancelDelete" class="btn btn-ghost">Batal</button>
                    <button wire:click="deleteItem" class="btn btn-error">
                        <span wire:loading.remove wire:target="deleteItem">Hapus</span>
                        <span wire:loading wire:target="deleteItem" class="loading loading-spinner loading-sm"></span>
                    </button>
                </div>
            </div>
            <div class="modal-backdrop" wire:click="cancelDelete"></div>
        </div>
    @endif
</div>
