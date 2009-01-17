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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_GoogleBase
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Base Feed Model
 *
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
     * @return Zend_Gdata_Feed
     */
    public function getFeed($location = null, $storeId = null)
    {
        $query = new Zend_Gdata_Query($location);
        return $this->getService($storeId)->getFeed($query);
    }

    /**
     * Retrieve Items Statistics (expires, clicks, views, impr. etc.)
     *
     * @return array
     */
    public function getItemsStatsArray($storeId = null)
    {
        $feed = $this->getFeed(self::ITEMS_LOCATION, $storeId);
        $result = array();
        foreach ($feed as $entry) {
            $draft = 'no';
            if (is_object($entry->getControl()) && is_object($entry->getControl()->getDraft())) {
                $draft = $entry->getControl()->getDraft()->getText();
            }
            $data = array(
                'draft'     => ($draft == 'yes' ? 1 : 0)
            );
            $elements = $entry->getExtensionElements();
            foreach ($elements as $el) {
                switch ($el->rootElement) {
                    case 'expiration_date':
                        $data['expires'] = Mage::getSingleton('googlebase/service_item')
                            ->gBaseDate2DateTime($el->getText());
                        break;

                    default:
                        break;
                }
            }

            $result[$entry->getId()->getText()] = $data;
        }
        return $result;
    }

    /**
     * Returns Google Base recommended Item Types
     *
     * @return array
     */
    public function getItemTypes($storeId = null)
    {
        if (is_array($this->_itemTypes)) {
            return $this->_itemTypes;
        }
        $location = self::ITEM_TYPES_LOCATION . '/' . Mage::app()->getLocale()->getLocale();
        $feed = $this->getFeed($location, $storeId);

        $itemTypes = array();
        foreach ($feed->entries as $entry) {
            $type = $entry->extensionElements[0]->text;
            $item = new Varien_Object();
            $item->setId($type);
            $item->setName($entry->title->text);
            $item->setLocation($entry->id->text);
            $itemTypes[$type] = $item;

            $typeAttributes = $entry->extensionElements[1]->extensionElements;
            $attributes = array();
            if (is_array($typeAttributes)) {
                foreach($typeAttributes as $attr) {
                    $name = $attr->extensionAttributes['name']['value'];
                    $type = $attr->extensionAttributes['type']['value'];
                    $attribute = new Varien_Object();
                    $attribute->setId($name);
                    $attribute->setName($name);
                    $attribute->setType($type);
                    $attributes[$name] = $attribute;
                }
            }
            ksort($attributes);
            $item->setAttributes($attributes);
        }
        ksort($itemTypes);
        $this->_itemTypes = $itemTypes;
        return $itemTypes;
    }

    /**
     * Returns Google Base Attributes
     *
     * @param string $type Google Base Item Type
     * @return array
     */
    public function getAttributes($type, $storeId = null)
    {
        $itemTypes = $this->getItemTypes($storeId);
        if (isset($itemTypes[$type]) && $itemTypes[$type] instanceof Varien_Object) {
            return $itemTypes[$type]->getAttributes();
        }
        Mage::throwException(Mage::helper('googlebase')->__('No such Item Type "%s" in Google Base to retrieve attributes', $type));
    }
}