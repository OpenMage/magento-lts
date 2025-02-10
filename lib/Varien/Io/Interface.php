<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Varien
 * @package    Varien_Io
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Input/output client interface
 *
 * @category   Varien
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
     * @param mixed $dir
     * @param mixed $mode
     * @param mixed $recursive
     */
    public function mkdir($dir, $mode = 0777, $recursive = true);

    /**
     * Delete a directory
     *
     * @param mixed $dir
     * @param mixed $recursive
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
     * @param mixed $dir
     */
    public function cd($dir);

    /**
     * Read a file
     *
     * @param mixed      $filename
     * @param null|mixed $dest
     */
    public function read($filename, $dest = null);

    /**
     * Write a file
     *
     * @param mixed      $filename
     * @param mixed      $src
     * @param null|mixed $mode
     */
    public function write($filename, $src, $mode = null);

    /**
     * Delete a file
     *
     * @param mixed $filename
     */
    public function rm($filename);

    /**
     * Rename or move a directory or a file
     *
     * @param mixed $src
     * @param mixed $dest
     */
    public function mv($src, $dest);

    /**
     * Chamge mode of a directory or a file
     *
     * @param mixed $filename
     * @param mixed $mode
     */
    public function chmod($filename, $mode);

    /**
     * Get list of cwd subdirectories and files
     *
     * @param null|mixed $grep
     */
    public function ls($grep = null);

    /**
     * Retrieve directory separator in context of io resource
     *
     */
    public function dirsep();
}
