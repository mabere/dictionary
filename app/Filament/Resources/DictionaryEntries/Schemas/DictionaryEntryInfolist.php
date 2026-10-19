<?php

namespace App\Filament\Resources\DictionaryEntries\Schemas;

use App\Models\DictionaryLemma;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class DictionaryEntryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Rincian lema')
                ->tabs([
                    Tab::make('Identitas & bentuk')->schema([
                        Section::make('Identitas leksikal')->schema([
                            TextEntry::make('headword')->label('Lema Tolaki')->weight('bold'),
                            TextEntry::make('orthographyProfile.name')->label('Profil ortografi')
                                ->formatStateUsing(fn (?string $state, DictionaryLemma $record): string => $state
                                    ? $state.' ('.$record->orthographyProfile?->version.')' : 'Belum ditetapkan'),
                            RepeatableEntry::make('forms')->label('Bentuk terkait')->schema([
                                TextEntry::make('form')->label('Bentuk'),
                                TextEntry::make('form_type')->label('Jenis')->badge(),
                                TextEntry::make('region_label')->label('Wilayah')->placeholder('Tidak ditetapkan'),
                                TextEntry::make('notes')->label('Catatan')->columnSpanFull(),
                                TextEntry::make('source.citation')->label('Sumber')->placeholder('Tidak dicatat')->columnSpanFull(),
                                RepeatableEntry::make('analyses')->label('Analisis morfologi')->schema([
                                    TextEntry::make('segmentation')->label('Segmentasi'),
                                    TextEntry::make('baseLemma.headword')->label('Lema dasar')->placeholder('Tidak ditetapkan'),
                                    TextEntry::make('morphemes')->label('Morfem')->formatStateUsing(fn ($state): string => is_array($state) ? implode(' + ', $state) : (string) $state)->placeholder('Tidak dicatat'),
                                    TextEntry::make('analysis_note')->label('Catatan analisis')->columnSpanFull(),
                                    TextEntry::make('status')->label('Status analisis')->badge(),
                                    TextEntry::make('source.citation')->label('Sumber')->placeholder('Tidak dicatat')->columnSpanFull(),
                                ])->columns(2)->columnSpanFull(),
                            ])->columns(2)->columnSpanFull()->placeholder('Belum ada bentuk alternatif atau turunan.'),
                        ])->columns(2),
                    ]),
                    Tab::make('Makna & padanan')->schema([
                        RepeatableEntry::make('senses')->label('Daftar makna')->schema([
                            TextEntry::make('sense_number')->label('Nomor makna')->badge(),
                            TextEntry::make('part_of_speech')->label('Kelas kata')->placeholder('Belum ditetapkan'),
                            TextEntry::make('category.name')->label('Kategori/domain')->placeholder('Belum ditetapkan'),
                            TextEntry::make('definition')->label('Definisi')->placeholder('Belum diisi')->columnSpanFull(),
                            TextEntry::make('definition_tolaki')->label('Definisi Tolaki')->placeholder('Tidak tersedia')->columnSpanFull(),
                            TextEntry::make('definition_english')->label('Definisi Inggris')->placeholder('Tidak tersedia')->columnSpanFull(),
                            TextEntry::make('grammatical_features')->label('Ciri gramatikal')->formatStateUsing(fn ($state): string => is_array($state) ? collect($state)->map(fn ($value, $key) => is_string($key) ? "{$key}: {$value}" : $value)->implode('; ') : (string) $state)->placeholder('Tidak dicatat'),
                            TextEntry::make('domain_label')->label('Domain')->placeholder('Tidak ditetapkan'),
                            TextEntry::make('register_label')->label('Register/ragam')->placeholder('Tidak ditetapkan'),
                            TextEntry::make('status')->label('Status makna')->badge(),
                            TextEntry::make('usage_note')->label('Catatan penggunaan')->columnSpanFull(),
                            TextEntry::make('cultural_note')->label('Catatan budaya')->columnSpanFull(),
                            TextEntry::make('source.citation')->label('Sumber makna')->placeholder('Tidak dicatat')->columnSpanFull(),
                            RepeatableEntry::make('translations')->label('Padanan')->schema([
                                TextEntry::make('language_code')->label('Bahasa')->badge(),
                                TextEntry::make('form')->label('Padanan'),
                                TextEntry::make('translation_type')->label('Jenis')->badge(),
                                TextEntry::make('context_note')->label('Konteks'),
                                TextEntry::make('status')->label('Status padanan')->badge(),
                                TextEntry::make('source.citation')->label('Sumber')->placeholder('Tidak dicatat')->columnSpanFull(),
                            ])->columns(3)->columnSpanFull()->placeholder('Belum ada padanan.'),
                            RepeatableEntry::make('relations')->label('Relasi makna')->schema([
                                TextEntry::make('relation_type')->label('Jenis relasi')->badge(),
                                TextEntry::make('relatedSense.sense_number')->label('Nomor makna terkait'),
                                TextEntry::make('relatedSense.lemma.headword')->label('Lema terkait'),
                                TextEntry::make('note')->label('Catatan')->columnSpanFull(),
                            ])->columns(3)->columnSpanFull()->placeholder('Belum ada relasi makna.'),
                        ])->columns(2)->columnSpanFull()->placeholder('Belum ada makna.'),
                    ]),
                    Tab::make('Contoh')->schema([
                        RepeatableEntry::make('senses')->label('Contoh berdasarkan makna')->schema([
                            TextEntry::make('sense_number')->label('Makna')->formatStateUsing(fn ($state): string => 'Makna '.$state),
                            TextEntry::make('definition')->label('Definisi')->placeholder('Belum diisi')->columnSpanFull(),
                            RepeatableEntry::make('examples')->label('Contoh penggunaan')->schema([
                                TextEntry::make('tolaki_text')->label('Kalimat Tolaki')->columnSpanFull(),
                                TextEntry::make('region_label')->label('Wilayah')->placeholder('Tidak ditetapkan'),
                                TextEntry::make('evidence_method')->label('Metode bukti')->badge(),
                                TextEntry::make('status')->label('Status contoh')->badge(),
                                TextEntry::make('elicitation_note')->label('Catatan elisitasi')->columnSpanFull(),
                                TextEntry::make('source.citation')->label('Sumber')->placeholder('Tidak dicatat')->columnSpanFull(),
                                RepeatableEntry::make('translations')->label('Terjemahan contoh')->schema([
                                    TextEntry::make('language_code')->label('Bahasa')->badge(),
                                    TextEntry::make('text')->label('Terjemahan')->columnSpanFull(),
                                    TextEntry::make('translation_note')->label('Catatan'),
                                    TextEntry::make('status')->label('Status terjemahan')->badge(),
                                    TextEntry::make('source.citation')->label('Sumber')->placeholder('Tidak dicatat'),
                                ])->columns(2)->columnSpanFull()->placeholder('Belum ada terjemahan contoh.'),
                            ])->columns(2)->columnSpanFull()->placeholder('Belum ada contoh.'),
                        ])->columns(2)->columnSpanFull()->placeholder('Belum ada makna.'),
                    ]),
                    Tab::make('Pelafalan')->schema([
                        RepeatableEntry::make('pronunciations')->label('Pelafalan lema')->schema([
                            TextEntry::make('transcription')->label('Transkripsi')->formatStateUsing(fn (?string $state): ?string => $state ? '/'.$state.'/' : null),
                            TextEntry::make('transcription_system')->label('Sistem transkripsi')->placeholder('Tidak ditetapkan'),
                            TextEntry::make('region_label')->label('Wilayah')->placeholder('Tidak ditetapkan'),
                            TextEntry::make('status')->label('Status pelafalan')->badge(),
                            TextEntry::make('audio_path')->label('Berkas audio')->placeholder('Tidak ada rekaman'),
                            TextEntry::make('rights_status')->label('Status hak')->badge(),
                            TextEntry::make('source.citation')->label('Sumber')->placeholder('Tidak dicatat')->columnSpanFull(),
                        ])->columns(2)->columnSpanFull()->placeholder('Belum ada data pelafalan.'),
                    ]),
                    Tab::make('Editorial & riwayat')->schema([
                        Section::make('Status editorial')->schema([
                            TextEntry::make('status')->label('Status')->badge()->color(fn (string $state): string => match ($state) {
                                'published' => 'success', 'reviewed' => 'info', 'rejected' => 'danger', 'submitted' => 'warning', default => 'gray',
                            }),
                            TextEntry::make('creator.name')->label('Pembuat')->placeholder('-'),
                            TextEntry::make('reviewer.name')->label('Reviewer')->placeholder('-'),
                            TextEntry::make('reviewed_at')->label('Waktu review')->dateTime()->placeholder('-'),
                            TextEntry::make('legacy_entry_id')->label('ID entri lama')->placeholder('-'),
                            TextEntry::make('created_at')->label('Dibuat')->dateTime(),
                            TextEntry::make('updated_at')->label('Diperbarui')->dateTime(),
                        ])->columns(3),
                        RepeatableEntry::make('editorialEvents')->label('Riwayat editorial')->schema([
                            TextEntry::make('action')->label('Tindakan')->badge(),
                            TextEntry::make('from_status')->label('Status sebelumnya')->placeholder('-'),
                            TextEntry::make('to_status')->label('Status berikutnya')->placeholder('-'),
                            TextEntry::make('actor.name')->label('Pelaku')->placeholder('Sistem'),
                            TextEntry::make('created_at')->label('Waktu')->dateTime(),
                            TextEntry::make('reason')->label('Alasan')->columnSpanFull(),
                        ])->columns(3)->placeholder('Belum ada perubahan status.'),
                    ]),
                    Tab::make('Sumber')->schema([
                        Section::make('Keterlacakan sumber')
                            ->description('Sumber di bawah ini terhubung ke komponen lema atau makna yang bersangkutan.')
                            ->schema([
                                RepeatableEntry::make('sourceReferences')
                                    ->label('Sumber terkait')
                                    ->state(fn (DictionaryLemma $record): array => static::collectSources($record))
                                    ->schema([
                                        TextEntry::make('citation')->label('Sitasi'),
                                        TextEntry::make('source_type')->label('Jenis')->badge(),
                                        TextEntry::make('rights_status')->label('Hak')->badge(),
                                        TextEntry::make('visibility')->label('Visibilitas')->badge(),
                                        TextEntry::make('uses')->label('Komponen yang didukung')->listWithLineBreaks()->columnSpanFull(),
                                    ])->columns(2)->columnSpanFull()->placeholder('Belum ada sumber yang ditautkan.'),
                            ]),
                    ]),
                ])
                ->contained()
                ->columnSpanFull(),
        ]);
    }

    private static function collectSources(DictionaryLemma $lemma): array
    {
        $references = [];
        $add = function ($source, string $usage) use (&$references): void {
            if (! $source) {
                return;
            }

            $id = $source->getKey();
            $references[$id] ??= [
                'citation' => $source->citation,
                'source_type' => $source->source_type,
                'rights_status' => $source->rights_status,
                'visibility' => $source->visibility,
                'uses' => [],
            ];
            $references[$id]['uses'][] = $usage;
        };

        foreach ($lemma->forms as $form) {
            $add($form->source, 'Bentuk: '.$form->form);
            foreach ($form->analyses as $analysis) {
                $add($analysis->source, 'Analisis morfologi: '.$form->form);
            }
        }

        foreach ($lemma->senses as $sense) {
            $add($sense->source, 'Definisi makna '.$sense->sense_number);
            foreach ($sense->translations as $translation) {
                $add($translation->source, 'Padanan '.strtoupper($translation->language_code).' makna '.$sense->sense_number);
            }
            foreach ($sense->examples as $example) {
                $add($example->source, 'Contoh makna '.$sense->sense_number);
                foreach ($example->translations as $translation) {
                    $add($translation->source, 'Terjemahan contoh '.strtoupper($translation->language_code).' makna '.$sense->sense_number);
                }
            }
            foreach ($sense->relations as $relation) {
                $add($relation->source, 'Relasi makna '.$sense->sense_number);
            }
        }

        foreach ($lemma->pronunciations as $pronunciation) {
            $add($pronunciation->source, 'Pelafalan lema');
        }

        return array_values(array_map(function (array $reference): array {
            $reference['uses'] = array_values(array_unique($reference['uses']));

            return $reference;
        }, $references));
    }
}
