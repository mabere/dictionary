<?php

namespace App\Filament\Resources\DictionaryEntries\Schemas;

use App\Models\DictionaryEntry;
use Closure;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class DictionaryEntryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Bagian entri kamus')
                ->tabs([
                    Tab::make('1. Identitas')
                        ->icon('heroicon-o-language')
                        ->schema([
                            Section::make('Lema Tolaki')
                                ->description('Masukkan bentuk utama sesuai kebijakan ejaan yang digunakan. Variasi dicatat sebagai bentuk terhubung.')
                                ->schema([
                                    TextInput::make('headword')
                                        ->label('Lema / headword Tolaki')
                                        ->required()
                                        ->maxLength(255)
                                        ->rules([
                                            fn ($record): \Closure => function (string $attribute, mixed $value, \Closure $fail) use ($record): void {
                                                $normalized = \App\Models\DictionaryLemma::normalizeForm((string) $value);
                                                $query = \App\Models\DictionaryLemma::query()
                                                    ->where('headword_normalized', $normalized);
                                                if ($record) {
                                                    $query->whereKeyNot($record->getKey());
                                                }
                                                if ($query->exists()) {
                                                    $fail("Lema dengan ejaan '{$value}' sudah ada di kamus. Silakan tambahkan makna baru pada lema yang sudah ada atau gunakan varian.");
                                                }
                                            },
                                        ]),
                                    Select::make('orthography_profile_id')->label('Profil ortografi')->relationship(
                                        'orthographyProfile',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->where('profile_type', 'orthography'),
                                    )->searchable()->preload()
                                        ->helperText('Pilih profil yang disahkan; biarkan kosong bila belum ada profil aktif.'),
                                    Repeater::make('forms')->label('Bentuk lain dan varian')->relationship()->schema([
                                        TextInput::make('form')->label('Bentuk')->required()->maxLength(255)->columnSpan(2),
                                        Select::make('form_type')->label('Jenis bentuk')->required()->options([
                                            'variant' => 'Varian ejaan/pelafalan',
                                            'derived' => 'Bentuk turunan',
                                            'subentry' => 'Subentri',
                                            'historical' => 'Bentuk historis',
                                        ])->columnSpan(1),
                                        TextInput::make('region_label')->label('Wilayah (bila terverifikasi)')->maxLength(255)->columnSpan(1),
                                        self::sourceField()->columnSpan(2),
                                        Textarea::make('notes')->label('Catatan bentuk')->rows(2)->columnSpanFull(),
                                        Repeater::make('analyses')->label('Analisis morfologi')->relationship()->schema([
                                            Select::make('base_lemma_id')->label('Lema dasar')->relationship('baseLemma', 'headword')->searchable()->preload()->columnSpan(2),
                                            Textarea::make('segmentation')->label('Segmentasi morfem')->rows(2)->columnSpan(2),
                                            Select::make('status')->label('Status analisis')->required()->options(['draft' => 'Belum ditinjau', 'reviewed' => 'Ditinjau'])->default('draft')->columnSpan(2),
                                            self::sourceField()->columnSpan(2),
                                            Textarea::make('analysis_note')->label('Catatan analisis')->rows(2)->columnSpanFull(),
                                        ])->grid(1)->columns(['default' => 1, 'lg' => 6])->columnSpanFull()->collapsible()->addActionLabel('Tambah analisis'),
                                    ])->grid(1)->columns(['default' => 1, 'lg' => 6])->columnSpanFull()->collapsible()
                                        ->itemLabel(fn (array $state): ?string => $state['form'] ?? 'Bentuk baru')
                                        ->addActionLabel('Tambah bentuk'),
                                ])->columns(2),
                        ]),
                    Tab::make('2. Makna & padanan')
                        ->icon('heroicon-o-queue-list')
                        ->schema([
                            Section::make('Daftar makna')
                                ->description('Satu lema dapat memiliki beberapa makna dan beberapa padanan dalam bahasa yang sama.')
                                ->schema([
                                    Repeater::make('senses')->label('Makna')->relationship()->schema([
                                        TextInput::make('sense_number')->label('Nomor makna')->numeric()->integer()->minValue(1)->required(),
                                        Select::make('part_of_speech')->label('Kelas kata')->options([
                                            'noun' => 'Nomina',
                                            'verb' => 'Verba',
                                            'adjective' => 'Adjektiva',
                                            'adverb' => 'Adverbia',
                                            'pronoun' => 'Pronomina',
                                            'preposition' => 'Preposisi',
                                            'conjunction' => 'Konjungsi',
                                            'interjection' => 'Interjeksi',
                                            'phrase' => 'Frasa/ungkapan',
                                        ])->searchable(),
                                        Select::make('category_id')->label('Kategori/domain')->relationship('category', 'name')->searchable()->preload(),
                                        Textarea::make('definition')->label('Definisi utama (bahasa Indonesia)')->rows(3)->columnSpanFull(),
                                        Textarea::make('definition_tolaki')->label('Definisi bahasa Tolaki (opsional)')->rows(3)->columnSpanFull(),
                                        Textarea::make('definition_english')->label('Definition in English (optional)')->rows(3)->columnSpanFull(),
                                        Textarea::make('usage_note')->label('Catatan penggunaan')->rows(2),
                                        Textarea::make('cultural_note')->label('Catatan budaya')->rows(2),
                                        self::sourceField(),
                                        Repeater::make('translations')->label('Padanan')->relationship()->schema([
                                            Select::make('language_code')->label('Bahasa')->required()->options(['id' => 'Indonesia', 'en' => 'Inggris']),
                                            TextInput::make('form')->label('Padanan')->required()->maxLength(255),
                                            Select::make('translation_type')->label('Jenis padanan')->required()->options([
                                                'equivalent' => 'Padanan langsung',
                                                'gloss' => 'Glos',
                                                'descriptive' => 'Deskriptif',
                                                'contextual' => 'Bergantung konteks',
                                            ])->default('equivalent'),
                                            Textarea::make('context_note')->label('Keterangan konteks')->rows(2)->columnSpanFull(),
                                            self::sourceField(),
                                        ])->columns(3)->minItems(1)->addActionLabel('Tambah padanan'),
                                        Repeater::make('examples')->label('Contoh penggunaan')->relationship()->schema([
                                            Textarea::make('tolaki_text')->label('Kalimat Tolaki')->required()->rows(2)->columnSpanFull(),
                                            Repeater::make('translations')->label('Terjemahan contoh')->relationship()->schema([
                                                Select::make('language_code')->label('Bahasa')->required()->options(['id' => 'Indonesia', 'en' => 'Inggris']),
                                                Textarea::make('text')->label('Terjemahan')->required()->rows(2),
                                                Textarea::make('translation_note')->label('Catatan')->rows(2),
                                                self::sourceField(),
                                            ])->columns(2)->addActionLabel('Tambah terjemahan'),
                                            TextInput::make('region_label')->label('Wilayah penggunaan')->maxLength(255),
                                            Textarea::make('elicitation_note')->label('Catatan sumber/elisitasi')->rows(2),
                                            Select::make('evidence_method')->label('Metode memperoleh contoh')->required()->options([
                                                'unknown' => 'Belum diketahui',
                                                'attested' => 'Pemakaian yang tercatat',
                                                'elicitation' => 'Elisitasi',
                                                'legacy_import' => 'Data lama yang diimpor',
                                            ])->default('unknown'),
                                            self::sourceField(),
                                        ])->collapsible()->itemLabel(fn (array $state): ?string => filled($state['tolaki_text'] ?? null)
                                                ? str($state['tolaki_text'])->limit(60)->toString() : 'Contoh baru')->addActionLabel('Tambah contoh'),
                                        Repeater::make('relations')->label('Relasi makna')->relationship()->schema([
                                            Select::make('related_sense_id')
                                                ->label('Makna terkait')
                                                ->relationship('relatedSense', 'definition')
                                                ->searchable()
                                                ->preload()
                                                ->required()
                                                ->helperText('Pilih makna yang menjadi target relasi. Relasi tanpa makna terkait tidak dapat disimpan.'),
                                            Select::make('relation_type')->label('Jenis relasi')->required()->options([
                                                'synonym' => 'Sinonim',
                                                'antonym' => 'Antonim',
                                                'see_also' => 'Lihat juga',
                                                'related' => 'Berkaitan',
                                            ]),
                                            Textarea::make('note')->label('Catatan relasi')->rows(2),
                                            self::sourceField(),
                                        ])->columns(2)->collapsible()->addActionLabel('Tambah relasi'),
                                    ])->minItems(1)
                                        ->itemLabel(fn (array $state): ?string => filled($state['sense_number'] ?? null) ? 'Makna '.$state['sense_number'] : 'Makna baru')
                                        ->collapsible()->addActionLabel('Tambah makna')
                                        ->rules([
                                            fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                                                $numbers = collect($value ?? [])->pluck('sense_number')->filter()->map(fn ($number) => (int) $number);
                                                if ($numbers->duplicates()->isNotEmpty()) {
                                                    $fail('Nomor makna dalam satu lema harus unik.');
                                                }
                                            },
                                        ]),
                                ]),
                        ]),
                    Tab::make('3. Pelafalan')
                        ->icon('heroicon-o-speaker-wave')
                        ->schema([
                            Section::make('Pelafalan lema')->schema([
                                Repeater::make('pronunciations')->label('Daftar pelafalan')->relationship()->schema([
                                    TextInput::make('transcription')->label('Transkripsi')->maxLength(500),
                                    TextInput::make('transcription_system')->label('Sistem transkripsi')->maxLength(64),
                                    TextInput::make('region_label')->label('Wilayah')->maxLength(255),
                                    FileUpload::make('audio_path')->label('Audio')->disk('public')->directory('pronunciations')
                                        ->acceptedFileTypes(['audio/mpeg', 'audio/wav', 'audio/ogg', 'audio/webm', 'audio/mp4'])->maxSize(5120),
                                    Select::make('audio_visibility')->label('Visibilitas audio')->options([
                                        'public' => 'Publik (Bisa didengar umum)',
                                        'internal' => 'Internal (Hanya tim redaksi)',
                                    ])->default('public')->required(),
                                    Select::make('rights_status')->label('Status hak audio')->required()->options([
                                        'unknown' => 'Belum diketahui',
                                        'permission_granted' => 'Izin diberikan',
                                        'public_domain' => 'Domain publik',
                                        'restricted' => 'Terbatas',
                                    ])->default('unknown'),
                                    self::sourceField(),
                                ])->columns(2)->collapsible()->addActionLabel('Tambah pelafalan'),
                            ]),
                        ]),
                    Tab::make('4. Editorial')
                        ->icon('heroicon-o-clipboard-document-check')
                        ->schema([
                            Section::make('Informasi editorial')->schema([
                                Select::make('status')->label('Status')->options([
                                    DictionaryEntry::STATUS_DRAFT => 'Draft',
                                    DictionaryEntry::STATUS_SUBMITTED => 'Menunggu review',
                                    DictionaryEntry::STATUS_REVIEWED => 'Ditinjau',
                                    DictionaryEntry::STATUS_PUBLISHED => 'Terbit',
                                    DictionaryEntry::STATUS_REJECTED => 'Perlu revisi',
                                ])->disabled()->dehydrated(false),
                                Select::make('reviewed_by')->label('Reviewer')->relationship('reviewer', 'name')->disabled()->dehydrated(false),
                                TextInput::make('legacy_entry_id')->label('ID entri lama')->disabled()->dehydrated(false),
                            ])->columns(3),
                        ]),
                ])
                ->contained()
                ->columnSpanFull(),
        ]);
    }

    private static function sourceField(): Select
    {
        return Select::make('source_id')->label('Sumber')->relationship('source', 'citation')->searchable()->preload();
    }
}
