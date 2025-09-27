<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_System
 */
class Mage_System_Dirs
{
    /**
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public static function rm($dirname)
    {
        if (is_array($dirname)) {
            $dirname = $dirname[1];
        }
        // Sanity check
        if (!@file_exists($dirname)) {
            return false;
        }

        // Simple delete for a file
        if (@is_file($dirname) || @is_link($dirname)) {
            return unlink($dirname);
        }

        // Create and iterate stack
        $stack = [$dirname];
        while ($entry = array_pop($stack)) {
            // Watch for symlinks
            if (@is_link($entry)) {
                @unlink($entry);
                continue;
            }

            // Attempt to remove the directory
            if (@rmdir($entry)) {
                continue;
            }

            // Otherwise add it to the stack
            $stack[] = $entry;
            $dh = opendir($entry);
            while (false !== $child = readdir($dh)) {
                // Ignore pointers
                if ($child === '.' || $child === '..') {
                    continue;
                }
                // Unlink files and add directories to stack
                $child = $entry . DIRECTORY_SEPARATOR . $child;
                if (is_dir($child) && !is_link($child)) {
                    $stack[] = $child;
                } else {
                    @unlink($child);
                }
            }
            @closedir($dh);
        }
        return true;
    }

    /**
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    public static function mkdirStrict($path, $recursive = true, $mode = 0777)
    {
        $exists = file_exists($path);
        if ($exists && is_dir($path)) {
            return true;
        }
        if ($exists && !is_dir($path)) {
            throw new Exception("'{$path}' already exists, should be a dir, not a file!");
        }
        $out = @mkdir($path, $mode, $recursive);
        if (false === $out) {
            throw new Exception("Can't create dir: '{$path}'");
        }
        return true;
    }

    public static function copyFileStrict($source, $dest)
    {
        $exists = file_exists($source);
        if (!$exists) {
            throw new Exception('No file exists: ' . $exists);
        }
    }
}
