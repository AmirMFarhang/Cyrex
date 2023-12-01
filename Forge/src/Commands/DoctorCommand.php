<?php

namespace Cyrex\Commands;

class DoctorCommand
{
    public bool $fix = false;
    public function checkDirectoryStructure($baseDir) {
        $requiredStructure = [
            'Src' => [
                'App.php' => 'file',
                'Controllers' => [
                    'After' => 'directory',
                    'AppController.php' => 'file',
                    'Before' => 'directory',
                ],
                'Helpers' => 'directory',
                'Initiate.php' => 'file',
                'Middleware' => 'directory',
                'Models' => [
                    'Entities' => 'directory',
                ],
                'View' => 'directory',
            ],
        ];

        $this->checkDirectoryStructureRecursive($baseDir, $requiredStructure, $baseDir);
    }

    public function checkDirectoryStructureRecursive($dir, $structure, $baseDir) {
        foreach ($structure as $item => $type) {
            $path = $dir . '/' . $item;

            if ($type === 'file') {
                if (!file_exists($path) || !is_file($path)) {
                    echo "Missing file: " . str_replace($baseDir . '/', '', $path) . "\n";
                    if($this->fix)
                    {
                        $sta = fopen($path, 'w+');
                        if(!$sta)
                            echo "Failed to fix the ".str_replace($baseDir . '/', '', $path) . "\n";
                        else
                            fclose($sta);
                    }
                }
            } elseif ($type === 'directory') {
                if (!file_exists($path) || !is_dir($path)) {
                    echo "Missing directory: " . str_replace($baseDir . '/', '', $path) . "\n";
                    if($this->fix)
                    {
                        $sta = mkdir($path);
                        if(!$sta)
                            echo "Failed to fix the ".str_replace($baseDir . '/', '', $path) . "\n";
                    }
                }
            } elseif (is_array($type)) {
                if (!is_dir($path)) {
                    echo "Missing directory: " . str_replace($baseDir . '/', '', $path) . "\n";
                    if($this->fix)
                    {
                        $sta = mkdir($path);
                        if(!$sta)
                            echo "Failed to fix the ".str_replace($baseDir . '/', '', $path) . "\n";
                    }
                } else {
                    $this->checkDirectoryStructureRecursive($path, $type, $baseDir);
                }
            }
        }
    }

    public function handle($args = null)
    {
        $baseDir = getcwd();
        if(isset($args[0]) && $args[0] == 'fix')
        {
            $this->fix = true;
        }
        $this->checkDirectoryStructure($baseDir);
        print_r("You can use command doctor fix to restructure the unavailable directories");

    }
}