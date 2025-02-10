<?php
/**
 * Media library image config interface
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Media
 */
interface Mage_Media_Model_Image_Config_Interface
{
    /**
     * Retrieve base url for media files
     *
     * @return string
     */
    public function getBaseMediaUrl();

    /**
     * Retrieve base path for media files
     *
     * @return string
     */
    public function getBaseMediaPath();

    /**
     * Retrieve url for media file
     *
     * @param string $file
     * @return string
     */
    public function getMediaUrl($file);

    /**
     * Retrieve file system path for media file
     *
     * @param string $file
     * @return string
     */
    public function getMediaPath($file);
}
