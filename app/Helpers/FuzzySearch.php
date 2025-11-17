<?php

namespace App\Helpers;

class FuzzySearch
{
    /**
     * Prepare fuzzy patterns for search.
     *
     * @param string $query
     * @return array
     */
    public static function matchPatterns(string $query): array
    {
        $query = trim($query);
        $likePattern = '%' . $query . '%';      // for LIKE
        $soundex = soundex($query);             // for soundex
        $metaphone = metaphone($query);         // for metaphone

        return [$likePattern, $query, $soundex, $metaphone];
    }

    /**
     * Check if two strings are similar using Levenshtein distance.
     *
     * @param string $a
     * @param string $b
     * @param int $threshold
     * @return bool
     */
    public static function isSimilar(string $a, string $b, int $threshold = 2): bool
    {
        return levenshtein(strtolower($a), strtolower($b)) <= $threshold;
    }
}
