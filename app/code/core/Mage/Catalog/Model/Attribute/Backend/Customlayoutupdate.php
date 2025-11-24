<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Product url key attribute backend
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Attribute_Backend_Customlayoutupdate extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Product custom layout update attribute validate function.
     * In case invalid data throws exception.
     *
     * @param Varien_Object $object
     * @return bool
     * @throws Mage_Eav_Model_Entity_Attribute_Exception
     */
    public function validate($object)
    {
        $attributeName = $this->getAttribute()->getName();
        $xml = trim((string) $object->getData($attributeName));

        if (!$this->getAttribute()->getIsRequired() && empty($xml)) {
            return true;
        }

        /** @var Mage_Adminhtml_Model_LayoutUpdate_Validator $validator */
        $validator = Mage::getModel('adminhtml/layoutUpdate_validator');
        if (!$validator->isValid($xml)) {
            $messages = $validator->getMessages();
            // add first message to exception
            $massage = array_shift($messages);
            $eavExc = new Mage_Eav_Model_Entity_Attribute_Exception($massage);
            $eavExc->setAttributeCode($attributeName);
            throw $eavExc;
        }

        return true;
    }
}
