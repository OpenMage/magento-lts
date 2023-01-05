<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Remember Me block
 *
 * @category   Mage
 * @package    Mage_Persistent
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method $this setHref(string $value)
 * @method $this setAnchorText(string $value)
 */
class Mage_Persistent_Block_Header_Additional extends Mage_Core_Block_Html_Link
{
    /**
     * Render additional header html
     *
     * @return string
     */
    protected function _toHtml()
    {
        $text = $this->__('(Not %s?)', $this->escapeHtml(Mage::helper('persistent/session')->getCustomer()->getName()));

        $this->setAnchorText($text);
        $this->setHref($this->getUrl('persistent/index/unsetCookie'));

        return parent::_toHtml();
    }
}
