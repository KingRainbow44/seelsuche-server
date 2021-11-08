<?php

require(getcwd()  . '/vendor/autoload.php');

use Symfony\Component\Finder\Finder;

/**
 * A build script to compile the server into one PHP 'phar executable'.
 * Run this script with the working directory as the server directory.
 */
$fileName = time() . '.phar';

if(file_exists(getcwd() . '/.builds/' . $fileName))
    unlink(getcwd() . '/.builds/' . $fileName);

# Create a PHAR instance.
$phar = new Phar(getcwd() . '/.builds/' . $fileName);

# Set the signature algorithm.
$phar->setSignatureAlgorithm(Phar::SHA256);
# Start buffering, allows for the modification of the stub.
$phar->startBuffering();

# Add source files, resource files, and composer files.
$entryPoint = getcwd() . "/seelsuche.php";
$phar->addFile($entryPoint, getRelativeFilePath(new SplFileInfo($entryPoint)));

$locations = [
    getcwd() . "/src" => "*.php",
    getcwd() . "/vendor" => "*.php",
    getcwd() . "/resources" => "*.json"
];
foreach($locations as $location => $name) {
    $fileFinder = new Finder();
    $fileFinder->files()
        ->ignoreVCS(true)
        ->name($name)
        ->exclude(["Tests", "tests", "docs"])
        ->in($location);
    foreach($fileFinder as $file)
        $phar->addFile($file->getRealPath(), getRelativeFilePath($file));
}

# Set entry-point file.
$entryPoint = 'seelsuche.php';
# Create "bootloader".
$bootLoader = $phar->createDefaultStub($entryPoint);
# Create final stub.
$bootLoader = "#!/usr/bin/env php\n" . $bootLoader;
# Set stub.
$phar->setStub($bootLoader);

# Stop buffering, close PHAR, and log to console that we are finished.
$phar->stopBuffering(); echo "PHAR has been created. Name: $fileName";

/**
 * Get file relative path
 * @param  \SplFileInfo $file
 * @return string
 */
function getRelativeFilePath(SplFileInfo $file): string
{
    $realPath   = $file->getRealPath();
    $pathPrefix = dirname(__DIR__) . DIRECTORY_SEPARATOR;

    $pos          = strpos($realPath, $pathPrefix);
    $relativePath = ($pos !== false) ? substr_replace($realPath, '', $pos, strlen($pathPrefix)) : $realPath;

    return strtr($relativePath, '\\', '/');
}