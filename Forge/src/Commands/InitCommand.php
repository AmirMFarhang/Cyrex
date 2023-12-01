<?php

namespace Cyrex\Commands;

class InitCommand
{
    public static function handle($args)
    {
        if (count($args) !== 1) {
            echo "Usage: Cyrex create <folderName>\n";
            return;
        }

        $folderName = $args[0];

        // Get the current working directory
        $currentDir = getcwd();

        // Specify the path where you want to create the folder
        $folderPath = $currentDir . DIRECTORY_SEPARATOR . $folderName;

        // Check if the folder already exists
        if (!file_exists($folderPath)) {
            // Create the folder
            mkdir($folderPath);
            echo "Folder created in: $folderPath" . PHP_EOL;
        } else {
            echo "Folder already exists in: $folderPath" . PHP_EOL;
        }
    }
}