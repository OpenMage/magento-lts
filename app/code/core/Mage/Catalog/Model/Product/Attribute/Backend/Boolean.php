<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */

/**
 * Product attribute for enable/disable option
 *
 * @package    Mage_Catalog
 */
class Mage_Catalog_Model_Product_Attribute_Backend_Boolean extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Set attribute default value if value empty
     *
     * @param  Varien_Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        if ($object->getData('use_config_' . $attributeCode)) {
            $object->setData($attributeCode, '');
        }

        return $this;
    }
}
