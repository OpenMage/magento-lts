<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer's tags edit block
 * This functionality was removed
 *
 * @deprecated  after 1.3.2.3
 * @category    Mage
 * @package     Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Block_Customer_Edit extends Mage_Core_Block_Template
{
    protected $_tag;

    public function getTag()
    {
        if( !$this->_tag ) {
            $this->_tag = Mage::registry('tagModel');
        }

        return $this->_tag;
    }

    public function getFormAction()
    {
        return $this->getUrl('*/*/save', array('tagId' => $this->getTag()->getTagId()));
    }

    public function getBackUrl()
    {
        return $this->getUrl('*/*/view', array('tagId' => $this->getTag()->getTagId()));
    }
}
