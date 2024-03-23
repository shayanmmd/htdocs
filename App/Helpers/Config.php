<?php

namespace App\Helpers;

include "vendor/autoload.php";

use App\Exceptions\ConfigFileNotFound;

class Config
{
    public static function getFileContents(string $fileName)
    {
        $filePath = realpath(__DIR__ . "/../Configs/" . $fileName . ".php");        
        if (!file_exists($filePath))
            throw new ConfigFileNotFound();
        $fileContents = require $filePath;
        return $fileContents;
    }
    public static function get(string $fileName, string $key)
    {
        $fileContents = self::getFileContents($fileName);
        if (is_null($key))
            return $fileContents;
        return $fileContents[$key] ?? null;
    }
}
