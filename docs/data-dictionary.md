# Kamus Data Linguistik (Rancangan)

Status: skema konseptual telah diterapkan pada database kosong melalui migrasi `2026_10_17_000001` sampai `2026_10_17_000006`. Beberapa label dan nilai terkontrol tetap perlu ditetapkan oleh editor.

## Entitas dan relasi

```text
Lemma 1 ── * LemmaForm
Lemma 1 ── * Sense 1 ── * Translation
                    ├── * Example 1 ── * ExampleTranslation
                    ├── * SenseRelation
                    └── * EvidenceLink * ── 1 Source
Lemma/Sense 1 ── * Pronunciation
```

| Entitas | Fungsi | Field konseptual |
|---|---|---|
| Lemma | Entri kepala Tolaki | ID, headword, nilai pencarian, status, pemilik, legacy ID |
| LemmaForm | Bentuk alternatif/rujuk | bentuk, tipe bentuk, lema rujuk, wilayah, sumber |
| Sense | Satu makna leksikal | nomor, definisi utama Indonesia, definisi Tolaki/Inggris opsional, kelas kata, domain, ragam, status, catatan |
| Translation | Padanan untuk suatu makna | kode bahasa, bentuk, jenis padanan, konteks, status validasi |
| Example | Bukti pemakaian yang terkait makna | teks Tolaki, wilayah, sumber, metode elisitasi |
| ExampleTranslation | Terjemahan contoh | kode bahasa, teks, penerjemah/sumber, status review |
| Pronunciation | Pelafalan | transkripsi, sistem, rekaman, pelafal/sumber, izin |
| Source | Sumber data | sitasi, jenis, tanggal, hak/izin, visibilitas, catatan |
| EditorialEvent | Jejak proses | entitas/versi, tindakan, pelaku, waktu, alasan |

## Pemetaan nama tabel implementasi

| Konsep | Tabel |
|---|---|
| Profil ortografi | `orthography_profiles` |
| Lema | `dictionary_lemmas` |
| Bentuk lema/varian | `dictionary_lemma_forms` |
| Makna | `dictionary_senses` |
| Padanan | `dictionary_translations` |
| Contoh Tolaki | `dictionary_examples` |
| Terjemahan contoh | `dictionary_example_translations` |
| Pelafalan | `dictionary_pronunciations` |
| Analisis morfologi | `dictionary_morphological_analyses` |
| Relasi makna | `dictionary_sense_relations` |
| Sumber | `dictionary_sources` |
| Tautan bukti tambahan | `dictionary_evidence_links` |
| Jejak editorial | `dictionary_editorial_events` |

## Aturan data

- Bahasa disimpan sebagai kode stabil (`tol`, `id`, `en`) dan label UI dikelola terpisah.
- Nilai asli tidak ditimpa oleh normalisasi pencarian.
- Satu lema boleh memiliki banyak makna, termasuk makna dengan kelas kata sama.
- Satu makna boleh memiliki banyak padanan dalam bahasa yang sama.
- Sumber dapat ditautkan ke lema, makna, bentuk, contoh, padanan, atau pelafalan karena bukti tiap unsur bisa berbeda.
- Field wajib, enum status, dan batas visibilitas sumber ditetapkan setelah pedoman editorial disahkan.
- Hubungan legacy disimpan supaya migrasi bisa ditelusuri dan direkonsiliasi.

## CSV terstruktur v1

Endpoint admin terautentikasi menyediakan template, ekspor, dan impor `dictionary-lexicographic-v1.csv`. Satu baris mewakili satu lema; `forms_json`, `senses_json`, dan `pronunciations_json` menyimpan array objek JSON UTF-8. Objek makna dapat memuat `translations`, `examples` (beserta terjemahannya), dan `relations`; bentuk dapat memuat `analyses`. `public_id` ULID dipakai sebagai kunci pembaruan. Impor menjalankan seluruh berkas dalam satu transaksi, mempertahankan data turunan yang tidak disertakan, dan tidak menghapus entri. Editor tidak dapat menerbitkan melalui impor; sumber baru dibuat internal dengan hak belum diketahui, dan hak audio selalu dikembalikan ke `unknown` untuk ditinjau.

Definisi Indonesia disimpan di `definition` dengan `definition_language=id`. Definisi opsional Tolaki dan Inggris disimpan pada `definition_tolaki` dan `definition_english` (migrasi `2026_10_17_000007`). Nilai lama dengan `definition_language=tol|en` tetap ditampilkan sebagai definisi utama berbahasa tersebut untuk menjaga kompatibilitas; saat penyuntingan, editor perlu memindahkannya ke field bahasa terkait bila ingin kembali menggunakan definisi utama Indonesia.

## Yang belum diputuskan

- Apakah dialek/wilayah berupa tabel referensi atau label wilayah bertingkat.
- Apakah definisi Inggris diperlukan untuk semua makna atau cukup padanan Inggris.
- Granularitas versi: seluruh entri atau versi per makna/komponen.
- Kebijakan penyimpanan metadata informan dan persetujuan publikasi.
