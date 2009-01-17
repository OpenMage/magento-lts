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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer's tags edit block
 *
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