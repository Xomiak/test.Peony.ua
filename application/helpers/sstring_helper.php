<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

function string_limit_words($string, $word_limit, $from = 0)
{
       $words = explode(' ', $string);
       $return = array_slice($words, $from, $word_limit);
       if(count($words) > $word_limit)
       array_pop($words);
       return implode(' ', $return);
}

function string_words_count($string)
{
    $words = explode(' ', $string);
    return count($words);
}

function getAnons($descr, $lettersCount = 120){
    $descr = htmlentities(strip_tags($descr));
    if($descr) {
        $pos = strpos($descr, '.', $lettersCount);
        $anons = $descr;
        if ($pos)
            $anons = substr($descr, 0, $pos+1);

        return $anons;
    }

    return false;
}