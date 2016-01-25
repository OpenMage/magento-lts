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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Xmlconnect form custom element
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Simplexml_Form_Element_Custom extends Mage_XmlConnect_Model_Simplexml_Form_Element_Abstract
{
    /**
     * Init custom element
     *
     * @param array $attributes
     */
    public function __construct($attributes = array())
    {
        parent::__construct($attributes);
        $this->setType('custom');
    }

    /**
     * Add required attributes to validator rule
     *
     * @throws Mage_Core_Exception
     * @param Mage_XmlConnect_Model_Simplexml_Element $xmlObj
     * @return Mage_XmlConnect_Model_Simplexml_Form_Element_Custom
     */
    protected  function _addRequiredAttributes(Mage_XmlConnect_Model_Simplexml_Element $xmlObj)
    {
        $this->_addId($xmlObj);

        foreach ($this->getRequiredXmlAttributes() as $attribute => $defValue) {
            $data = $this->getData($this->_underscore($attribute));

            if (null !== $data) {
                $xmlObj->addAttribute($attribute, $xmlObj->xmlAttribute($data));
            } elseif(null !== $defValue){
                $xmlObj->addAttribute($attribute, $xmlObj->xmlAttribute($defValue));
            } else {
                Mage::throwException(Mage::helper('xmlconnect')->__('%s attribute is required.', $attribute));
            }
        }
        return $this;
    }
}
