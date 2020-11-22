<?php

set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) {
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

$resourcesRoot = implode(DIRECTORY_SEPARATOR, [getcwd(), 'resources']); // path to laravel resources folder
$path = [$resourcesRoot, 'lang']; // path to your lang folder
$translates = []; // array for result translations. used globally from scanForTranslates()

try {
    $fp = fopen(implode(DIRECTORY_SEPARATOR, array_merge($path, ['lang.translations.json'])), 'w');
    $json = json_encode(scanForTranslates($path));
    fwrite($fp, $json);
    fclose($fp);
} catch (\Exception $e) {
    print('ERROR! ' . $e->getMessage());
}

/**
 * @param array $path - path to your 'lang' folder. Must be an array and
 * as the first element must be a string value of the path to 'resources' folder
 * @return array - every lang folder as array with keys as files (or as subfolders)
 */
function scanForTranslates($path)
{
    global $translates;
    $strPath = implode(DIRECTORY_SEPARATOR, $path);
    
    $nodes = scandir($strPath);

    foreach ($nodes as $node) {
        if (strrpos($node, '.') === false) { //node is a folder
            $path[] = $node;
            scanForTranslates($path);
            array_pop($path);
        } elseif (strrpos($node, '.php') !== false) { // node is a php translate file
            $array = createMultidimensionalArray(array_slice($path, 1), [], array_merge($path, [$node]));
            $translates = array_merge_recursive($translates, $array);
        }
    }

    return $translates;
}

/**
 * Create array from path to translation file, e.g. path "en/subfolder/auth.php"
 * should be considered as $array["en"]["subfolder"]["auth"] = <array_from_auth.php>
 * @param $keys - array of folders which are gonna be keys in result array
 * @param $initial
 * @param $includePath - path to included translation file
 * @return mixed - multidimensional array
 */
function createMultidimensionalArray($keys, $initial, $includePath)
{
    $key = array_shift($keys);
    
    if (count($keys) === 0) {
        $file = end($includePath);
        $fileName = mb_substr($file, 0, strrpos($file, '.'));
        $initial[$key][$fileName] = include(implode(DIRECTORY_SEPARATOR, $includePath));
    } else {
        $initial[$key] = createMultidimensionalArray($keys, [], $includePath);
    }

    return $initial;
}

