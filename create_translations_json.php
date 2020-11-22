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
    
    print_r(json_encode(scanForTranslates($path)));
} catch (\Exception $e) {
    print('ERROR! ' . $e->getMessage());
}


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

