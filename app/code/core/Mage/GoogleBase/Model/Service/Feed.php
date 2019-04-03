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
 * @package     Mage_GoogleBase
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Base Feed Model
 *
 * @deprecated after 1.5.1.0
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Model_Service_Feed extends Mage_GoogleBase_Model_Service
{
    const ITEM_TYPES_LOCATION = 'http://www.google.com/base/feeds/itemtypes';
    const ITEMS_LOCATION = 'http://www.google.com/base/feeds/items';

    /**
     * Google Base Feed Instance
     *
     * @param string $location
     * @param int $storeId Store Id
     * @return Zend_Gdata_Feed
     */
    public function getFeed($location = null, $storeId = null)
    {
        $query = new Zend_Gdata_Query($location);
        return $this->getService($storeId)->getFeed($query);
    }

    /**
     * Retrieve Items Statistics
     *
     * @param string $id Google Base item ID
     *        (e.g. http://www.google.com/base/feeds/items/3613244304072139222 or 3613244304072139222)
     *
     * @param int $storeId Store Id
     * @return array
     */
    public function getItemStats($id, $storeId = null)
    {
        if (!stristr($id, 'http://')) {
            $id = self::ITEMS_LOCATION . '/' . $id;
        }
        if (!strstr($id, '?')) {
            $id = $id . '?content=attributes,meta';
        } elseif (!stristr($id, 'content=')) {
            $id = $id . '&content=attributes,meta';
        }
        try {
            $entry = $this->getService($storeId)->getGbaseItemEntry($id);
            return $this->_getEntryStats($entry);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Return item stats array based on Zend Gdata Entry object
     *
     * @param Zend_Gdata_Gbase_ItemEntry $entry
     * @return array
     */
    protected function _getEntryStats($entry)
    {
        $result = $_stats = array();

        $draft = 'no';
        if (is_object($entry->getControl()) && is_object($entry->getControl()->getDraft())) {
            $draft = $entry->getControl()->getDraft()->getText();
        }
        $result['draft'] = ($draft == 'yes' ? 1 : 0);

        $expirationDate = $entry->getGbaseAttribute('expiration_date');
        if (isset($expirationDate[0]) && is_object($expirationDate[0])) {
            $result['expires'] = Mage::getSingleton('googlebase/service_item')
                                    ->gBaseDate2DateTime($expirationDate[0]->getText());
        }

        $allAttributes = $entry->getExtensionElements();
        $elementsCount = count($allAttributes);
        if($elementsCount) {
            $statsElement = null;
            for ($i = 0; $i < $elementsCount; $i++) {
                /**
                 * @var $extAttribute Zend_Gdata_App_Extension_Element
                 */
                $extAttribute = $allAttributes[$i];
                if ((string)$extAttribute->rootElement == 'stats') {
                    $statsElement = $extAttribute;
                    break;
                }
            }
            if ($statsElement) {
                $_stats = $statsElement->getExtensionElements();
            }
            $statsCount = count($_stats);
            for ($i = 0; $i < $statsCount; $i++) {
                /**
                 * @var $_currentElement Zend_Gdata_App_Extension_Element
                 */
                $_currentElement = $_stats[$i];
                $_currentAttributes = $_currentElement->getExtensionAttributes();
                if (isset($_currentAttributes['total']) && isset($_currentAttributes['total']['value'])) {
                    $result[(string)$_currentElement->rootElement] = $_currentAttributes['total']['value'];
                }
            }
        }
        return $result;
    }

    /**
     * Returns Google Base recommended Item Types
     *
     * @param string $targetCountry Two-letters country ISO code
     * @return array
     */
    public function getItemTypes($targetCountry)
    {
        $locale = Mage::getSingleton('googlebase/config')->getCountryInfo($targetCountry, 'locale');
        $location = self::ITEM_TYPES_LOCATION . '/' . $locale;

        $itemTypes = array();
        foreach ($this->getGuestService()->getFeed($location)->entries as $entry) {
            if (isset($entry->extensionElements[1])) { // has attributes node?
                $typeAttributes = $entry->extensionElements[1]->extensionElements;
                if (is_array($typeAttributes) && !empty($typeAttributes)) { // only items with attributes allowed
                    $type = $entry->extensionElements[0]->text;
                    $item = new Varien_Object();
                    $item->setId($type);
                    $item->setName($entry->title->text);
                    $item->setLocation($entry->id->text);
                    $itemTypes[$type] = $item;

                    $attributes = array();
                    foreach($typeAttributes as $attr) {
                        $name = $attr->extensionAttributes['name']['value'];
                        $type = $attr->extensionAttributes['type']['value'];
                        $attribute = new Varien_Object();
                        $attribute->setId($name);
                        $attribute->setName($name);
                        $attribute->setType($type);
                        $attributes[$name] = $attribute;
                    }
                    ksort($attributes);
                    $item->setAttributes($attributes);
                }
            }
        }
        ksort($itemTypes);
        $this->_itemTypes = $itemTypes;
        return $itemTypes;
    }

    /**
     * Returns Google Base Attributes
     *
     * @param string $type Google Base Item Type
     * @param string $targetCountry Two-letters country ISO code
     * @return array
     */
    public function getAttributes($type, $targetCountry)
    {
        $itemTypes = $this->getItemTypes($targetCountry);
        if (isset($itemTypes[$type]) && $itemTypes[$type] instanceof Varien_Object) {
            return $itemTypes[$type]->getAttributes();
        }
        return array();
    }
}
