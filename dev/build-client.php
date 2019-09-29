<?php

/*
 * Bear CMS Universal
 * https://github.com/bearcms/universal
 * Copyright (c) Amplilabs Ltd.
 * Free to use under the MIT license.
 */

ini_set('max_execution_time', 0);

$sourceDir = realpath(__DIR__ . '/..');

exec('C:\dev\bin\composer\composer.bat update -d ' . $sourceDir . ' --no-dev --optimize-autoloader');

$makeDir = function (string $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
};

$tempDir = sys_get_temp_dir() . '/bearcms-universal-client-' . md5(uniqid());
$makeDir($tempDir);
//echo $tempDir . "\n";

$version = file_get_contents($sourceDir . '/VERSION');

$getFiles = function (string $dir) {
    $rootDir = rtrim($dir, '/\\');
    $_getFiles = function (string $dir) use (&$_getFiles, $rootDir) {
        $resultFiles = [];
        $files = scandir($rootDir . '/' . $dir);
        foreach ($files as $fileName) {
            if ($fileName != "." && $fileName != ".." && $fileName != ".git") {
                if (is_dir($rootDir . '/' . $dir . $fileName)) {
                    $resultFiles = array_merge($resultFiles, $_getFiles($dir . $fileName . '/'));
                } else {
                    $resultFiles[] = $dir . $fileName;
                }
            }
        }
        return $resultFiles;
    };
    return $_getFiles('');
};

$copyFile = function (string $source, string $target) use ($makeDir) {
    $makeDir(pathinfo($target, PATHINFO_DIRNAME));
    copy($source, $target);
};

$files = $getFiles($sourceDir . '/vendor');
foreach ($files as $filename) {
    $copyFile($sourceDir . '/vendor/' . $filename, $tempDir . '/src/vendor/' . $filename);
}

$files = $getFiles($sourceDir . '/src');
foreach ($files as $filename) {
    $copyFile($sourceDir . '/src/' . $filename, $tempDir . '/src/src/' . $filename);
}

$copyFile($sourceDir . '/autoload.php', $tempDir . '/src/autoload.php');


$indexContent = '<?php
include __DIR__ . \'/src/vendor/autoload.php\';
include __DIR__ . \'/src/autoload.php\';
';
file_put_contents($tempDir . '/autoload.php', $indexContent);

$zipName = 'bearcms-universal-client-' . $version . '.zip';
$zip = new \ZipArchive();
$zip->open($zipName, \ZipArchive::CREATE);
$directory = new \RecursiveDirectoryIterator($tempDir);
$iterator = new \RecursiveIteratorIterator($directory);
foreach ($iterator as $info) {
    $filename = str_replace('\\', '/', $info->getFilename());
    if ($filename !== '.' && $filename !== '..') {
        $pathName = $info->getPathname();
        $localFilename = str_replace('\\', '/', substr($pathName, strlen($tempDir) + 1));
        $zip->addFile($pathName, $localFilename);
    }
}
$zip->close();

$pharName = 'bearcms-universal-client-' . $version . '.phar';
$phar = new Phar(__DIR__ . '/' . $pharName, 0, $pharName);
$phar->buildFromDirectory($tempDir);
$phar->setStub($phar->createDefaultStub("autoload.php"));
echo 'Done';
