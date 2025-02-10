<?php
/**
 * Interface for work with archives
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Archive
 */
interface Mage_Archive_Interface
{
    /**
    * Pack file or directory.
    *
    * @param string $source
    * @param string $destination
    * @return string
    */
    public function pack($source, $destination);

    /**
    * Unpack file or directory.
    *
    * @param string $source
    * @param string $destination
    * @return string
    */
    public function unpack($source, $destination);
}
