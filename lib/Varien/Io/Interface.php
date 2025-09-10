<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Io
 */

/**
 * Input/output client interface
 *
 * @package    Varien_Io
 */
interface Varien_Io_Interface
{
    /**
     * Open a connection
     *
     */
    public function open(array $args = []);

    /**
     * Close a connection
     *
     */
    public function close();

    /**
     * Create a directory
     *
     */
    public function mkdir($dir, $mode = 0777, $recursive = true);

    /**
     * Delete a directory
     *
     */
    public function rmdir($dir, $recursive = false);

    /**
     * Get current working directory
     *
     */
    public function pwd();

    /**
     * Change current working directory
     *
     */
    public function cd($dir);

    /**
     * Read a file
     *
     */
    public function read($filename, $dest = null);

    /**
     * Write a file
     *
     */
    public function write($filename, $src, $mode = null);

    /**
     * Delete a file
     *
     */
    public function rm($filename);

    /**
     * Rename or move a directory or a file
     *
     */
    public function mv($src, $dest);

    /**
     * Chamge mode of a directory or a file
     *
     */
    public function chmod($filename, $mode);

    /**
     * Get list of cwd subdirectories and files
     *
     */
    public function ls($grep = null);

    /**
     * Retrieve directory separator in context of io resource
     *
     */
    public function dirsep();
}
