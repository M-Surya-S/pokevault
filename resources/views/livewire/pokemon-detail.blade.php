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

    @if($error)
        <div class="flex flex-col items-center justify-center py-20">
            <div class="alert alert-error max-w-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="stroke-current shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ $error }}</span>
            </div>
            <a href="/" class="btn btn-ghost mt-4" wire:navigate>← Kembali</a>
        </div>
    @elseif(empty($pokemon))
        <div class="flex justify-center py-20">
            <span class="loading loading-spinner loading-lg text-primary"></span>
        </div>
    @else
        {{-- Breadcrumb --}}
        <div class="breadcrumbs text-sm mb-6">
            <ul>
                <li><a href="/" wire:navigate>Jelajah</a></li>
                <li class="capitalize">{{ $pokemon['name'] }}</li>
            </ul>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left: Image & Basic Info --}}
            <div class="lg:col-span-1">
                <div class="card bg-base-100 shadow-xl">
                    <figure class="px-6 pt-6 bg-gradient-to-br from-primary/10 to-secondary/10 rounded-t-2xl">
                        <img
                            src="{{ $pokemon['image_url'] }}"
                            alt="{{ $pokemon['name'] }}"
                            class="w-48 h-48 object-contain drop-shadow-lg"
                        />
                    </figure>
                    <div class="card-body items-center text-center">
                        <span class="text-base-content/50 font-mono">#{{ str_pad($pokemon['id'], 3, '0', STR_PAD_LEFT) }}</span>
                        <h1 class="card-title text-2xl capitalize">{{ $pokemon['name'] }}</h1>

                        {{-- Types --}}
                        <div class="flex gap-2 mt-2">
                            @foreach($pokemon['types'] as $type)
                                @php
                                    $typeColors = [
                                        'normal' => 'badge-neutral',
                                        'fire' => 'bg-orange-500 text-white',
                                        'water' => 'bg-blue-500 text-white',
                                        'electric' => 'bg-yellow-400 text-black',
                                        'grass' => 'bg-green-500 text-white',
                                        'ice' => 'bg-cyan-300 text-black',
                                        'fighting' => 'bg-red-700 text-white',
                                        'poison' => 'bg-purple-500 text-white',
                                        'ground' => 'bg-amber-600 text-white',
                                        'flying' => 'bg-indigo-300 text-black',
                                        'psychic' => 'bg-pink-500 text-white',
                                        'bug' => 'bg-lime-500 text-white',
                                        'rock' => 'bg-yellow-700 text-white',
                                        'ghost' => 'bg-purple-700 text-white',
                                        'dragon' => 'bg-indigo-600 text-white',
                                        'dark' => 'bg-gray-700 text-white',
                                        'steel' => 'bg-gray-400 text-black',
                                        'fairy' => 'bg-pink-300 text-black',
                                    ];
                                @endphp
                                <span class="badge {{ $typeColors[$type] ?? 'badge-neutral' }} capitalize font-medium">
                                    {{ $type }}
                                </span>
                            @endforeach
                        </div>

                        {{-- Physical Stats --}}
                        <div class="stats stats-vertical sm:stats-horizontal shadow mt-4 w-full">
                            <div class="stat place-items-center py-3">
                                <div class="stat-title text-xs">Tinggi</div>
                                <div class="stat-value text-lg">{{ $pokemon['height'] / 10 }}m</div>
                            </div>
                            <div class="stat place-items-center py-3">
                                <div class="stat-title text-xs">Berat</div>
                                <div class="stat-value text-lg">{{ $pokemon['weight'] / 10 }}kg</div>
                            </div>
                        </div>

                        {{-- Collection Status --}}
                        <div class="mt-4 w-full">
                            @if($isCollected)
                                <div class="alert alert-success py-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="text-sm">Sudah di koleksi</span>
                                </div>
                            @else
                                @if($showAddForm)
                                    <div class="card bg-base-200 p-4">
                                        <h3 class="font-semibold mb-3">Tambah ke Koleksi</h3>
                                        <div class="form-control gap-3">
                                            <div>
                                                <label class="label py-1"><span class="label-text text-xs">Nickname (opsional)</span></label>
                                                <input type="text" wire:model="nickname" placeholder="Beri nickname..." class="input input-bordered input-sm w-full" maxlength="30" />
                                                @error('nickname') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="label py-1"><span class="label-text text-xs">Level</span></label>
                                                <input type="number" wire:model="level" min="1" max="100" class="input input-bordered input-sm w-full" />
                                                @error('level') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div>
                                                <label class="label py-1"><span class="label-text text-xs">Catatan (opsional)</span></label>
                                                <textarea wire:model="notes" placeholder="Tulis catatan..." class="textarea textarea-bordered textarea-sm w-full" maxlength="200" rows="2"></textarea>
                                                @error('notes') <span class="text-error text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="flex gap-2">
                                                <button wire:click="addToCollection" class="btn btn-primary btn-sm flex-1">
                                                    <span wire:loading.remove wire:target="addToCollection">Simpan</span>
                                                    <span wire:loading wire:target="addToCollection" class="loading loading-spinner loading-sm"></span>
                                                </button>
                                                <button wire:click="toggleAddForm" class="btn btn-ghost btn-sm">Batal</button>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <button wire:click="toggleAddForm" class="btn btn-primary w-full gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                        </svg>
                                        Tambah ke Koleksi
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right: Stats & Abilities --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Base Stats --}}
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-lg mb-4">Base Stats</h2>
                        <div class="space-y-3">
                            @foreach($pokemon['stats'] as $stat)
                                @php
                                    $statLabels = [
                                        'hp' => 'HP',
                                        'attack' => 'Attack',
                                        'defense' => 'Defense',
                                        'special-attack' => 'Sp. Atk',
                                        'special-defense' => 'Sp. Def',
                                        'speed' => 'Speed',
                                    ];
                                    $statColors = [
                                        'hp' => 'progress-error',
                                        'attack' => 'progress-warning',
                                        'defense' => 'progress-info',
                                        'special-attack' => 'progress-secondary',
                                        'special-defense' => 'progress-accent',
                                        'speed' => 'progress-success',
                                    ];
                                    $label = $statLabels[$stat['name']] ?? $stat['name'];
                                    $color = $statColors[$stat['name']] ?? 'progress-primary';
                                    $percentage = min(($stat['base_stat'] / 255) * 100, 100);
                                @endphp
                                <div class="flex items-center gap-3">
                                    <span class="w-20 text-sm text-base-content/70 text-right">{{ $label }}</span>
                                    <span class="w-10 text-sm font-bold text-right">{{ $stat['base_stat'] }}</span>
                                    <progress class="progress {{ $color }} flex-1" value="{{ $stat['base_stat'] }}" max="255"></progress>
                                </div>
                            @endforeach

                            @php
                                $totalStats = collect($pokemon['stats'])->sum('base_stat');
                            @endphp
                            <div class="divider my-1"></div>
                            <div class="flex items-center gap-3">
                                <span class="w-20 text-sm font-bold text-right">Total</span>
                                <span class="w-10 text-sm font-bold text-right">{{ $totalStats }}</span>
                                <progress class="progress progress-primary flex-1" value="{{ $totalStats }}" max="720"></progress>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Abilities --}}
                <div class="card bg-base-100 shadow-xl">
                    <div class="card-body">
                        <h2 class="card-title text-lg mb-4">Abilities</h2>
                        <div class="flex flex-wrap gap-2">
                            @foreach($pokemon['abilities'] as $ability)
                                <div class="badge badge-lg {{ $ability['is_hidden'] ? 'badge-ghost badge-outline' : 'badge-primary badge-outline' }} capitalize gap-1 py-3">
                                    {{ str_replace('-', ' ', $ability['name']) }}
                                    @if($ability['is_hidden'])
                                        <span class="text-xs opacity-60">(Hidden)</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
