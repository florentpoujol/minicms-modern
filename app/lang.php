<?php

namespace App;

class Lang
{
    public static function get($key, $lang = null)
    {
        return $key;
    }
}

/*
$dictionaries = [];
require_once "../languages/en.php";

$lang = isset($_GET["lang"]) ? $_GET["lang"] : "en";
if ($lang !== "en") {
  require_once "../languages/$lang.php";
}

function lang($key, $_lang = null)
{
  global $lang, $dictionaries;
  if (isset($_lang) === false) {
    $_lang = $lang;
  }
  $dico = $dictionaries[$_lang];

  $keys = explode(".", $key);
  foreach ($keys as $_key) {
    if (isset($dico[$_key]) === true) {
      $dico = $dico[$_key];
    }
    elseif ($_lang !== "en") {
      return lang($key, "en");
    }
  }

  if (is_array($dico)) {
    $dico = $key;
  }

  return $dico;
}
*/
