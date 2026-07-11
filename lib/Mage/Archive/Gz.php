<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Archive
 */

/**
 * Class to work with gz archives
 *
 * @package    Mage_Archive
 */
class Mage_Archive_Gz extends Mage_Archive_Abstract implements Mage_Archive_Interface
{
    /**
     * Pack file by GZ compressor.
     *
     * @param  string $source
     * @param  string $destination
     * @return string
     */
    public function pack($source, $destination)
    {
        $fileReader = new Mage_Archive_Helper_File($source);
        $fileReader->open('r');

        $archiveWriter = new Mage_Archive_Helper_File_Gz($destination);
        $archiveWriter->open('wb9');

        while (!$fileReader->eof()) {
            $archiveWriter->write($fileReader->read());
        }

        $fileReader->close();
        $archiveWriter->close();

        return $destination;
    }

    /**
     * Unpack file by GZ compressor.
     *
     * @param  string $source
     * @param  string $destination
     * @return string
     */
    public function unpack($source, $destination)
    {
        if (is_dir($destination)) {
            $file = $this->getFilename($source);
            $destination .= $file;
        }

        $archiveReader = new Mage_Archive_Helper_File_Gz($source);
        $archiveReader->open('r');

        $fileWriter = new Mage_Archive_Helper_File($destination);
        $fileWriter->open('w');

        while (!$archiveReader->eof()) {
            $fileWriter->write($archiveReader->read());
        }

        return $destination;
    }
}
