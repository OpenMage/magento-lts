<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Page
 */

/**
 * @package    Mage_Page
 */
class Mage_Page_Block_Js_Cookie extends Mage_Core_Block_Template
{
    /**
     * Get cookie model instance
     *
     * @return Mage_Core_Model_Cookie
     */
    public function getCookie()
    {
        return Mage::getSingleton('core/cookie');
    }

    /**
     * Get configured cookie domain
     *
     * @return string
     */
    public function getDomain()
    {
        $domain = $this->getCookie()->getDomain();
        if (!empty($domain[0]) && ($domain[0] !== '.')) {
            return '.' . $domain;
        }

        return $domain;
    }

    /**
     * Get configured cookie path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->getCookie()->getPath();
    }
}
