<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Archive
 */

/**
 * Class to work with archives
 *
 * @package    Mage_Archive
 */
class Mage_Archive_Abstract
{
    /**
     * Write data to file. If file can't be opened - throw exception
     *
     * @param  string         $destination
     * @param  string         $data
     * @return bool
     * @throws Mage_Exception
     */
    protected function _writeFile($destination, $data)
    {
        $destination = trim($destination);
        if (false === file_put_contents($destination, $data)) {
            throw new Mage_Exception("Can't write to file: " . $destination);
        }

        return true;
    }

    /**
     * Read data from file. If file can't be opened, throw to exception.
     *
     * @param  string         $source
     * @return string
     * @throws Mage_Exception
     *
     * @SuppressWarnings("PHPMD.ErrorControlOperator")
     */
    protected function _readFile($source)
    {
        $data = '';
        if (is_file($source) && is_readable($source)) {
            $data = @file_get_contents($source);
            if ($data === false) {
                throw new Mage_Exception("Can't get contents from: " . $source);
            }
        }

        return $data;
    }

    /**
     * Get file name from source (URI) without last extension.
     *
     * @param  string       $source
     * @param  bool         $withExtension
     * @return mixed|string
     */
    public function getFilename($source, $withExtension = false)
    {
        $file = str_replace(dirname($source) . DS, '', $source);
        if (!$withExtension) {
            return substr($file, 0, strrpos($file, '.'));
        }

        return $file;
    }
}
