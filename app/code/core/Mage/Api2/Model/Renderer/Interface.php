<?php

/**
 * Webservice API2 renderer adapter interface
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Api2
 */
interface Mage_Api2_Model_Renderer_Interface
{
    /**
     * Render content in a certain format
     *
     * @param array|object $data
     * @return string
     */
    public function render($data);

    /**
     * Get MIME type generated by renderer
     *
     * @return string
     */
    public function getMimeType();
}
