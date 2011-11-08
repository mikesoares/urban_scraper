<?php

/**
 * Urban Dictionary Scraper Class
 */
class UrbanScraper {

    /**
     * Grab the raw page
     *
     * @param string $letter
     * @param int $page
     * @return array 
     */
    protected static function _getPage($letter, $page = 1) {
        $urbanUrl = 'http://www.urbandictionary.com/browse.php?character=' . $letter . '&page=' . $page;
        $options = array(
            CURLOPT_URL             => $urbanUrl,
            CURLOPT_HEADER          => false,
            CURLOPT_RETURNTRANSFER  => true
        );

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $result = curl_exec($ch);
        curl_close($ch);
        return self::_parsePage($result, $letter, $page);
    }

    /**
     * Parse the page
     *
     * @param string $html
     * @param string $letter
     * @param int $currentPage
     * @return array
     */
    protected static function _parsePage($html, $letter, $currentPage) {
        $doc = new DOMDocument;
        $doc->loadHTML($html);
        $words = explode("\n", $doc->getElementById('columnist')->nodeValue);

        $goodWords = array();

        foreach ($words as $word) {
            $word = trim(strtolower($word));
            if ($word[0] == strtolower($letter) && $word[1] == ' ') {
                $word = substr($word, 2, strlen($word) - 1);
            }
            
            if (
                str_word_count($word) > 1 ||
                preg_match("/[^A-Za-z]/", $word) !== 0 ||
                strlen($word) < 2
            ) {
                continue;
            }

            $goodWords[] = trim($word);
        }

        if ($currentPage === 1) {
            $totalPages = $doc->getElementById('paginator')->lastChild->previousSibling->previousSibling->previousSibling->lastChild->previousSibling->previousSibling->nodeValue;

            for ($i = 2; $i <= (int)$totalPages; $i++) {
                foreach (self::_getPage($letter, $i) as $newWord) {
                    $goodWords[] = $newWord;
                }
            }
        }

        return $goodWords;
    }

    /**
     * Gets all of the words associated with the first $letter
     *
     * @param string $letter
     * @return array
     */
    public static function getWordList($letter) {
        return self::_getPage($letter);
    }
}
