<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_ProductAlert
 */

/**
 * @package    Mage_ProductAlert
 * @deprecated after 1.4.1.0
 *
 * @see Mage_ProductAlert_Block_Product_View
 */
class Mage_ProductAlert_Block_Price extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('productalert/price.phtml');
    }

    /**
     * @return bool
     */
    public function isShow()
    {
        if (!Mage::getStoreConfig('catalog/productalert/allow_price')) {
            return false;
        }

        return true;
    }

    /**
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return Mage::helper('productalert')->getSaveUrl('price');
    }
}
