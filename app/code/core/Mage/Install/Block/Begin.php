<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Install
 */

/**
 * Installation begin block
 *
 * @package    Mage_Install
 */
class Mage_Install_Block_Begin extends Mage_Install_Block_Abstract
{
    /**
     * Set template
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('install/begin.phtml');
    }

    /**
     * @deprecated
     */
    public function getLanguages() {}

    /**
     * Get wizard URL
     *
     * @return string
     */
    public function getPostUrl()
    {
        return Mage::getUrl('install/wizard/beginPost');
    }

    /**
     * Get License HTML contents
     *
     * @return string
     */
    public function getLicenseHtml()
    {
        return nl2br(file_get_contents(BP . DS . (string) Mage::getConfig()->getNode('install/eula_file')));
    }
}
