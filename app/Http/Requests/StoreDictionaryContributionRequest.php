<?php

namespace App\Http\Requests;

use App\Models\DictionaryEntry;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreDictionaryContributionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            '_contribution_token' => ['required', 'string'],
            'website' => ['prohibited'],
            'tolaki_word' => ['required', 'string', 'max:255', $this->uniqueDictionaryEntryRule()],
            'indonesian_word' => ['required', 'string', 'max:255'],
            'english_word' => ['nullable', 'string', 'max:255'],
            'part_of_speech' => [
                'nullable',
                'string',
                Rule::in([
                    'noun',
                    'verb',
                    'adjective',
                    'adverb',
                    'pronoun',
                    'preposition',
                    'conjunction',
                    'interjection',
                    'phrase',
                ]),
            ],
            'category_id' => ['nullable', 'integer', 'exists:dictionary_categories,id'],
            'definition' => ['nullable', 'string', 'max:5000'],
            'usage_note' => ['nullable', 'string', 'max:5000'],
            'cultural_note' => ['nullable', 'string', 'max:5000'],
            'tolaki_sentence' => ['nullable', 'required_with:indonesian_sentence,english_sentence', 'string', 'max:5000'],
            'indonesian_sentence' => ['nullable', 'required_with:tolaki_sentence,english_sentence', 'string', 'max:5000'],
            'english_sentence' => ['nullable', 'string', 'max:5000'],
            'example_notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $sessionToken = $this->session()->get('dictionary_contribution_token');
            $formToken = $this->input('_contribution_token');

            if (! is_string($sessionToken) || ! is_string($formToken) || ! hash_equals($sessionToken, $formToken)) {
                $validator->errors()->add('_contribution_token', 'Form usulan tidak valid. Silakan muat ulang halaman dan kirim kembali.');
            }
        });
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            '_contribution_token' => 'token form',
            'website' => 'website',
            'tolaki_word' => 'kata Tolaki',
            'indonesian_word' => 'arti Indonesia',
            'english_word' => 'padanan Inggris',
            'part_of_speech' => 'kelas kata',
            'category_id' => 'kategori',
            'tolaki_sentence' => 'kalimat Tolaki',
            'indonesian_sentence' => 'kalimat Indonesia',
            'english_sentence' => 'kalimat Inggris',
        ];
    }

    private function uniqueDictionaryEntryRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            $duplicateKey = DictionaryEntry::makeDuplicateKey(
                $value,
                $this->input('indonesian_word'),
                'Tolaki',
                'Indonesia',
                $this->input('part_of_speech'),
            );

            if (DictionaryEntry::query()->where('duplicate_key', $duplicateKey)->exists()) {
                $fail('Usulan dengan kombinasi kata, bahasa, dan kelas kata yang sama sudah ada.');

                return;
            }

            $normalizedHeadword = \App\Models\DictionaryLemma::normalizeForm($value);
            $normalizedIndonesian = \App\Models\DictionaryLemma::normalizeForm((string) $this->input('indonesian_word'));
            $lemmaExists = \App\Models\DictionaryLemma::query()
                ->where('headword_normalized', $normalizedHeadword)
                ->whereHas('senses.translations', function ($transQuery) use ($normalizedIndonesian): void {
                    $transQuery->where('language_code', 'id')
                        ->where('form_normalized', $normalizedIndonesian);
                })->exists();

            if ($lemmaExists) {
                $fail('Usulan dengan kombinasi kata Tolaki dan padanan Indonesia yang sama sudah ada di kamus.');
            }
        };
    }
}
