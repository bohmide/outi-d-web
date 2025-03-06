<?php
namespace App\Service;

class BadWordFilterService
{
    private array $badWords = [ 'insulte', 'offensant', 'viol', 'gfsfhdjgk',
        'attaque', 'brutalité', 'meurtre', 'agression', 'torture', 'massacre', 'harcèlement', 'menacer', 'fusillade', 'terrorisme',
        'racisme', 'xénophobie', 'discrimination', 'haine', 'sectarisme', 'intolérance', 'suprémacisme', 'oppression', 'ségrégation', 'extrémisme',
        'nudité', 'pornographie', 'explicite', 'pervers', 'séduction forcée', 'exploitation', 'prostitution', 'mineur', 'pédophilie', 'non-consentement',
        'overdose', 'addiction', 'cannabis', 'cocaïne', 'héroïne', 'ivresse', 'toxicomanie', 'stupéfiants', 'substance illicite', 'hallucinogène',
        'dépression', 'suicide', 'souffrance', 'détresse', 'automutilation', 'se faire du mal', 'désespoir', 'aide psychologique', 'pensées suicidaires',
        'vol', 'arnaque', 'corruption', 'escroquerie', 'blanchiment', 'hacking', 'cybercriminalité', 'piratage', 'marché noir', 'contrebande'];

    public function filter(string $text): string
    {
        foreach ($this->badWords as $word) {
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $text = preg_replace($pattern, str_repeat('*', strlen($word)), $text);
        }
        return $text;
    }

    public function containsBadWord(string $text): bool
    {
        foreach ($this->badWords as $word) {
            if (stripos($text, $word) !== false) {
                return true;
            }
        }
        return false;
    }
} 