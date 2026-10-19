<x-filament::page>
    @php
        $tabs = [
            ['key' => 'overview', 'label' => 'Mulai di sini', 'icon' => 'heroicon-o-sparkles'],
            ['key' => 'identity', 'label' => 'Identitas lema', 'icon' => 'heroicon-o-language'],
            ['key' => 'meaning', 'label' => 'Makna & padanan', 'icon' => 'heroicon-o-queue-list'],
            ['key' => 'pronunciation', 'label' => 'Pelafalan & editorial', 'icon' => 'heroicon-o-speaker-wave'],
            ['key' => 'workflow', 'label' => 'Simpan & penilaian', 'icon' => 'heroicon-o-clipboard-document-check'],
        ];
        $guideSections = $this->guideTabs();
        $examples = [
            'overview' => [
                'Kesiapan sebelum mulai',
                'Kode sumber / catatan lapangan',
                'Siapkan bukti yang dapat ditelusuri sebelum membuka form.',
            ],
            'identity' => [
                'Contoh field identitas',
                'Lema / kata dasar (headword) Tolaki',
                '[bentuk yang tercatat pada sumber]',
                'Bentuk lain: [varian yang memiliki bukti]',
            ],
            'meaning' => [
                'Contoh satu makna',
                'Definisi utama (bahasa Indonesia)',
                '[kategori semantik] yang [ciri pembeda berdasarkan bukti]',
                'Padanan dan contoh ditambahkan pada makna yang sama.',
            ],
            'pronunciation' => [
                'Contoh pencatatan pelafalan',
                'Sistem transkripsi',
                '[nama sistem yang digunakan]',
                'Audio hanya diunggah jika sumber dan izinnya jelas.',
            ],
            'workflow' => [
                'Contoh catatan tindak lanjut',
                'Catatan revisi',
                'Definisi dipersempit berdasarkan sumber [kode/halaman].',
                'Catat alasan perubahan agar proses review dapat ditelusuri.',
            ],
        ];
    @endphp

    <style>
        .guide-page {
            max-width: 88rem;
            margin-inline: auto;
            display: grid;
            gap: 1.5rem;
        }

        .guide-hero {
            border: 1px solid rgb(75 85 99 / .45);
            border-radius: 1rem;
            padding: 1.75rem;
            background: radial-gradient(ellipse at top right, rgb(99 102 241 / .16), transparent 55%), linear-gradient(135deg, rgb(31 41 55), rgb(17 24 39));
        }

        .guide-hero-row {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 1.5rem;
        }

        .guide-kicker {
            color: rgb(165 180 252);
            font-size: .75rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
        }

        .guide-title {
            margin-top: .5rem;
            color: white;
            font-size: 1.75rem;
            font-weight: 700;
        }

        .guide-intro {
            max-width: 44rem;
            margin-top: .5rem;
            color: rgb(209 213 219);
            font-size: .9rem;
            line-height: 1.6;
        }

        .guide-steps {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .75rem;
            margin-top: 1.5rem;
            border-top: 1px solid rgb(156 163 175 / .25);
            padding-top: 1rem;
        }

        .guide-step {
            color: rgb(229 231 235);
            font-size: .85rem;
        }

        .guide-step b {
            margin-right: .45rem;
            color: rgb(165 180 252);
            font-family: ui-monospace, monospace;
        }

        .guide-content {
            overflow: hidden;
            border: 1px solid rgb(75 85 99 / .5);
            border-radius: 1rem;
            background: rgb(17 24 39 / .55);
        }

        .guide-content-inner {
            padding: 1.5rem;
        }

        .guide-content-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 19rem;
            gap: 1.5rem;
        }

        .guide-example {
            align-self: start;
            border: 1px solid rgb(99 102 241 / .35);
            border-radius: .85rem;
            padding: 1.1rem;
            background: linear-gradient(160deg, rgb(49 46 129 / .25), rgb(17 24 39 / .7));
        }

        .guide-example h2 {
            margin: 0;
            color: rgb(224 231 255);
            font-size: .9rem;
            font-weight: 700;
        }

        .guide-example-label {
            display: block;
            margin: .9rem 0 .35rem;
            color: rgb(209 213 219);
            font-size: .75rem;
            font-weight: 600;
        }

        .guide-example-field {
            border: 1px solid rgb(75 85 99);
            border-radius: .55rem;
            padding: .65rem .75rem;
            background: rgb(3 7 18 / .55);
            color: rgb(243 244 246);
            font-size: .85rem;
        }

        .guide-example-note {
            margin-top: .75rem;
            color: rgb(156 163 175);
            font-size: .75rem;
            line-height: 1.5;
        }

        .guide-example-caution {
            display: flex;
            gap: .5rem;
            margin-top: 1rem;
            border-radius: .55rem;
            padding: .7rem;
            background: rgb(120 53 15 / .3);
            color: rgb(253 230 138);
            font-size: .72rem;
            line-height: 1.5;
        }

        .guide-next {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-top: 1.5rem;
            border: 1px solid rgb(217 119 6 / .35);
            border-radius: .75rem;
            padding: .85rem 1rem;
            background: rgb(120 53 15 / .22);
            color: rgb(253 230 138);
            font-size: .85rem;
        }

        @media(max-width: 900px) {
            .guide-content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width: 640px) {
            .guide-hero-row {
                align-items: start;
                flex-direction: column;
            }

            .guide-steps {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .guide-content-inner {
                padding: 1rem;
            }
        }

        .guide-hero { border-color: rgb(199 210 254); background: radial-gradient(ellipse at top right, rgb(99 102 241 / .13), transparent 55%), linear-gradient(135deg, white, rgb(238 242 255)); }
        .guide-kicker, .guide-step b { color: rgb(67 56 202); }
        .guide-title { color: rgb(17 24 39); }
        .guide-intro { color: rgb(75 85 99); }
        .guide-step { color: rgb(55 65 81); }
        .guide-content { border-color: rgb(209 213 219); background: white; }
        .guide-example { border-color: rgb(199 210 254); background: linear-gradient(160deg, rgb(238 242 255), white); }
        .guide-example h2 { color: rgb(49 46 129); }
        .guide-example-label { color: rgb(75 85 99); }
        .guide-example-field { border-color: rgb(209 213 219); background: white; color: rgb(31 41 55); }
        .guide-example-note { color: rgb(107 114 128); }
        .guide-example-caution { background: rgb(254 243 199); color: rgb(120 53 15); }
        .guide-next { border-color: rgb(253 230 138); background: rgb(255 251 235); color: rgb(120 53 15); }
        .dark .guide-hero { border-color: rgb(75 85 99 / .45); background: radial-gradient(ellipse at top right, rgb(99 102 241 / .16), transparent 55%), linear-gradient(135deg, rgb(31 41 55), rgb(17 24 39)); }
        .dark .guide-kicker, .dark .guide-step b { color: rgb(165 180 252); }
        .dark .guide-title { color: white; }
        .dark .guide-intro { color: rgb(209 213 219); }
        .dark .guide-step { color: rgb(229 231 235); }
        .dark .guide-content { border-color: rgb(75 85 99 / .5); background: rgb(17 24 39 / .55); }
        .dark .guide-example { border-color: rgb(99 102 241 / .35); background: linear-gradient(160deg, rgb(49 46 129 / .25), rgb(17 24 39 / .7)); }
        .dark .guide-example h2 { color: rgb(224 231 255); }
        .dark .guide-example-label { color: rgb(209 213 219); }
        .dark .guide-example-field { border-color: rgb(75 85 99); background: rgb(3 7 18 / .55); color: rgb(243 244 246); }
        .dark .guide-example-note { color: rgb(156 163 175); }
        .dark .guide-example-caution { background: rgb(120 53 15 / .3); color: rgb(253 230 138); }
        .dark .guide-next { border-color: rgb(217 119 6 / .35); background: rgb(120 53 15 / .22); color: rgb(253 230 138); }
    </style>

    <div class="guide-page">
        <section class="guide-hero">
            <div class="guide-hero-row">
                <div>
                    <p class="guide-kicker">Pusat bantuan · Kamus Tolaki</p>
                    <h1 class="guide-title">Panduan Mahasiswa</h1>
                    <p class="guide-intro">Panduan praktis mengumpulkan bukti, mengisi entri, dan menindaklanjuti review.
                        Ikuti tab berurutan sesuai alur kerja.</p>
                </div>
                <a href="{{ \App\Filament\Resources\DictionaryEntries\DictionaryEntryResource::getUrl('index') }}"
                    class="fi-btn fi-btn-color-primary fi-size-md">
                    <x-filament::icon icon="heroicon-o-rectangle-stack" /> Buka Lema dan Makna
                </a>
            </div>
            <div class="guide-steps">
                @foreach ([['01', 'Siapkan bukti'], ['02', 'Isi per bagian'], ['03', 'Periksa & simpan'], ['04', 'Tindak lanjuti review']] as [$number, $label])
                    <div class="guide-step"><b>{{ $number }}</b>{{ $label }}</div>
                @endforeach
            </div>
        </section>

        <div class="guide-content">
            <x-filament::tabs label="Bagian panduan input entri" class="overflow-x-auto px-3 pt-3 sm:px-5">
                @foreach ($tabs as $tab)
                    <x-filament::tabs.item :active="$selectedGuideTab === $tab['key']" :icon="$tab['icon']"
                        wire:click="selectGuideTab('{{ $tab['key'] }}')">
                        {{ $tab['label'] }}
                    </x-filament::tabs.item>
                @endforeach
            </x-filament::tabs>

            @php($tab = collect($tabs)->firstWhere('key', $selectedGuideTab))
            @if ($tab)
                <section id="guide-panel-{{ $tab['key'] }}" role="tabpanel" class="guide-content-inner">
                    <div class="guide-content-grid">
                        <article class="fi-prose max-w-none min-w-0 dark:prose-invert">
                            {!! $guideSections[$tab['key']] ?? '' !!}
                        </article>
                        <aside class="guide-example">
                            <div class="flex items-center gap-2">
                                <x-filament::icon icon="heroicon-o-pencil-square" />
                                <h2>{{ $examples[$tab['key']][0] }}</h2>
                            </div>
                            <div>
                                <span class="guide-example-label">{{ $examples[$tab['key']][1] }}</span>
                                <div class="guide-example-field">{{ $examples[$tab['key']][2] }}</div>
                                @if (filled($examples[$tab['key']][3] ?? null))
                                    <p class="guide-example-note">{{ $examples[$tab['key']][3] }}</p>
                                @endif
                            </div>
                            <div class="guide-example-caution">
                                <x-filament::icon icon="heroicon-o-light-bulb" />
                                <span>Contoh ini menunjukkan format pengisian, bukan data Tolaki yang telah
                                    diverifikasi.</span>
                            </div>
                        </aside>
                    </div>
                    @if ($tab['key'] === 'overview')
                        <div class="guide-next">
                            <span>Entri mahasiswa tersimpan sebagai draf. Jangan mengarang data untuk melengkapi field
                                opsional.</span>
                            <button type="button" wire:click="selectGuideTab('identity')" class="font-semibold">Mulai
                                dari identitas <x-filament::icon icon="heroicon-o-arrow-right" /></button>
                        </div>
                    @endif
                </section>
            @endif
        </div>
    </div>
</x-filament::page>
