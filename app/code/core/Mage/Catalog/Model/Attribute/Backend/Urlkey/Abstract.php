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
abstract class Mage_Catalog_Model_Attribute_Backend_Urlkey_Abstract extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Format url key attribute before save, also use product name as url key if it empty
     *
     * @param Varien_Object $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attributeName = $this->getAttribute()->getName();

        $urlKey = $object->getData($attributeName);
        if ($urlKey === false) {
            return $this;
        }
        if ($urlKey == '') {
            $urlKey = $object->getName();
        }

        if (method_exists($object, 'setLocale')) {
            $locale = Mage::getStoreConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_LOCALE, $object->getStoreId());
            $object->setLocale($locale);
        }

        $object->setData($attributeName, $object->formatUrlKey($urlKey));

        return $this;
    }

    /**
     * Executes after url attribute save.
     *
     * @param Varien_Object $object
     *
     * @return $this
     */
    public function afterSave($object)
    {
        /**
         * This logic moved to Mage_Catalog_Model_Indexer_Url
         */
        return $this;
    }
}
