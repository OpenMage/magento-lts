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
 * @package     Mage_Connect
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block for authors
 *
 * @category    Mage
 * @package     Mage_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Authors
    extends Mage_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Abstract
{
    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('connect')->__('Authors');
    }

    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('connect')->__('Authors');
    }

    /**
     * Return add author button html
     *
     * @return string
     */
    public function getAddAuthorButtonHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setType('button')
            ->setClass('add')
            ->setLabel($this->__('Add Author'))
            ->setOnClick('addAuthor()')
            ->toHtml();
    }

    /**
     * Return array of authors
     *
     * @return array
     */
    public function getAuthors()
    {
        $authors = array();
        if ($this->getData('authors')) {
            $temp = array();
            foreach ($this->getData('authors') as $param => $values) {
                if (is_array($values)) {
                    foreach ($values as $key => $value) {
                        $temp[$key][$param] =$value;
                    }
                }
            }
            foreach ($temp as $key => $value) {
                $authors[$key] = Mage::helper('core')->jsonEncode($value);
            }
        }
        return $authors;
    }
}
