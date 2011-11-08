<?php

require_once 'includes/UrbanScraper.class.php';

/** WARNING: This will take a while... */

// which letters do we want to check
$letters = 'abcdefghijklmnopqrstuvwxyz';
$letterArr = preg_split('//', $letters, -1, PREG_SPLIT_NO_EMPTY);
$allWords = array();

// initialize some arrays
foreach ($letterArr as $char) {
    $letter{strtoupper($char)} = array();
}

// scrape the page and organize the words into respective arrays
foreach ($letterArr as $char) {
    foreach (UrbanScraper::getWordList($char) as $word) {
        $letter{strtoupper($word[0])}[] = $word;
    }
}

// keep unique words from each array and output them
foreach ($letterArr as $char) {
    $letter{strtoupper($char)} = array_unique($letter{strtoupper($char)});
    foreach ($letter{strtoupper($char)} as $word) {
        echo $word . "\n";
    }
}
