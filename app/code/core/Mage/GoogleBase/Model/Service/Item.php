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
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Base Item Model
 *
 * @deprecated after 1.5.1.0
 * @category   Mage
 * @package    Mage_GoogleBase
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleBase_Model_Service_Item extends Mage_GoogleBase_Model_Service
{
    const DEFAULT_ITEM_TYPE = 'products';
    const DEFAULT_ATTRIBUTE_TYPE = 'text';

    /**
     * Object instance to populate entry data
     *
     * @var Varien_Object
     */
    protected $_object = null;

    /**
     * Item instance to update entry data
     *
     * @var Mage_GoogleBase_Model_Item
     */
    protected $_item = null;

    /**
     * $_object Setter
     *
     * @param Varien_Object $object
     * @return Mage_GoogleBase_Model_Service_Item
     */
    public function setObject($object)
    {
        $this->_object = $object;
        return $this;
    }

    /**
     * $_object Getter
     *
     * @return Varien_Object
     */
    public function getObject()
    {
        return $this->_object;
    }

    /**
     * $_item Setter
     *
     * @param Mage_GoogleBase_Model_Item $item
     * @return Mage_GoogleBase_Model_Service_Item
     */
    public function setItem($item)
    {
        $this->_item = $item;
        return $this;
    }

    /**
     * $_item Getter
     *
     * @return Mage_GoogleBase_Model_Item
     */
    public function getItem()
    {
        return $this->_item;
    }

    /**
     * Return Store level Service Instance
     *
     * @return Zend_Gdata_Gbase
     */
    public function getService($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->getStoreId();
        }
        return parent::getService($storeId);
    }

    /**
     * Insert Item into Google Base
     *
     * @return Zend_Gdata_Gbase_ItemEntry
     */
    public function insert()
    {
        $this->_checkItem();
        $service = $this->getService();
        $entry = $service->newItemEntry();
        $this->setEntry($entry);
        $this->_prepareEnrtyForSave();
        $this->getEntry()->setItemType($this->_getItemType());
        $entry = $service->insertGbaseItem($this->getEntry());
        $this->setEntry($entry);
        $entryId = $this->getEntry()->getId();
        $published = $this->gBaseDate2DateTime($this->getEntry()->getPublished()->getText());
        $this->getItem()
            ->setGbaseItemId($entryId)
            ->setPublished($published);

        if ($expires = $this->_getAttributeValue('expiration_date')) {
            $expires = $this->gBaseDate2DateTime($expires);
            $this->getItem()->setExpires($expires);
        }
    }

    /**
     * Update Item data in Google Base
     *
     * @return Zend_Gdata_Gbase_ItemEntry
     */
    public function update()
    {
        $this->_checkItem();
        $service = $this->getService();
        $entry = $service->getGbaseItemEntry( $this->getItem()->getGbaseItemId() );
        $this->setEntry($entry);
        $this->_prepareEnrtyForSave();
        $entry = $service->updateGbaseItem($this->getEntry());

    }

    /**
     * Delete Item from Google Base
     *
     * @return Zend_Gdata_Gbase_ItemFeed
     */
    public function delete()
    {
        $this->_checkItem();

        $service = $this->getService();
        $entry = $service->getGbaseItemEntry( $this->getItem()->getGbaseItemId() );
        return $service->deleteGbaseItem($entry, $this->getDryRun());
    }

    /**
     * Hide item in Google Base
     *
     * @return Mage_GoogleBase_Model_Service_Item
     */
    public function hide()
    {
        $this->_saveDraft(true);
        return $this;
    }

    /**
     * Publish item in Google Base
     *
     * @return Mage_GoogleBase_Model_Service_Item
     */
    public function activate()
    {
        $this->_saveDraft(false);
        return $this;
    }

    /**
     * Update item Control property
     *
     * @param boolean Save as draft or not
     * @return Mage_GoogleBase_Model_Service_Item
     */
    protected function _saveDraft($yes = true)
    {
        $this->_checkItem();

        $service = $this->getService();
        $entry = $service->getGbaseItemEntry( $this->getItem()->getGbaseItemId() );

        $draftText = $yes ? 'yes' : 'no';
        $draft = $service->newDraft($draftText);
        $control = $service->newControl($draft);

        $entry->setControl($control);
        $entry->save();
        return $this;
    }

    /**
     * Prepare Entry data and attributes before saving in Google Base
     *
     * @return Mage_GoogleBase_Model_Service_Item
     */
    protected function _prepareEnrtyForSave()
    {
        $object = $this->getObject();
        if (!($object instanceof Varien_Object)) {
            Mage::throwException(Mage::helper('googlebase')->__('Object model is not specified to save Google Base entry.'));
        }

        $this->_setUniversalData();

        $attributes = $this->getAttributeValues();
        if (is_array($attributes) && count($attributes)) {
            foreach ($attributes as $name => $data) {

                $name = $this->_normalizeString($name);
                $value = isset($data['value']) ? $data['value'] : '';
                $type  = isset($data['type']) && $data['type'] ? $data['type'] : self::DEFAULT_ATTRIBUTE_TYPE;

                $customSetter = '_setAttribute' . ucfirst($name);
                if (method_exists($this, $customSetter)) {
                    $this->$customSetter($name, $value, $type);
                } else {
                    $this->_setAttribute($name, $value, $type);
                }
            }
        }
        return $this;
    }

    /**
     * Remove characters and words not allowed by Google Base in title and content (description).
     *
     * (to avoid "Expected response code 200, got 400.
     * Reason: There is a problem with the character encoding of this attribute")
     *
     * @param string $string
     * @return string
     */
    protected function _cleanAtomAttribute($string)
    {
        return Mage::helper('core/string')
            ->substr(preg_replace('/[\pC¢€•—™°½]|shipping/ui', '', $string), 0, 3500);
    }

    /**
     * Assign values to universal attribute of entry
     *
     * @return Mage_GoogleBase_Model_Service_Item
     */
    protected function _setUniversalData()
    {
        $service = $this->getService();
        $object = $this->getObject();
        $entry = $this->getEntry();
        $attributeValues = $this->getAttributeValues();

        $this->_setAttribute('id', $object->getId() . '_' . $this->getStoreId(), 'text');

        if (isset($attributeValues['title']['value'])) {
            $titleText = $attributeValues['title']['value'];
            unset($attributeValues['title']); // to prevent "Reason: Duplicate title" error
        } elseif ($object->getName()) {
            $titleText = $object->getName();
        } else {
            $titleText = 'no title';
        }
        $entry->setTitle($service->newTitle()->setText($this->_cleanAtomAttribute($titleText)));

        if ($object->getUrl()) {
            $links = $entry->getLink();
            if (!is_array($links)) {
                $links = array();
            }
            $link = $service->newLink();
            $link->setHref($object->getUrl());
            $link->setRel('alternate');
            $link->setType('text/html');
            if ($object->getName()) {
                $link->setTitle($object->getName());
            }
            $links[0] = $link;
            $entry->setLink($links);
        }

        if (isset($attributeValues['description']['value'])) {
            $descrText = $attributeValues['description']['value'];
            unset($attributeValues['description']); // to prevent "Reason: Duplicate description" error
        } elseif ($object->getDescription()) {
            $descrText = $object->getDescription();
        } else {
            $descrText = 'no description';
        }
        $entry->setContent($service->newContent()->setText($this->_cleanAtomAttribute($descrText)));

        if (isset($attributeValues['price']['value']) && floatval($attributeValues['price']['value']) > 0) {
            $price = $attributeValues['price']['value'];
        } else {
            $price = $object->getPrice();
        }

        $this->_setAttributePrice(false, $price);

        if ($object->getQuantity()) {
            $quantity = $object->getQuantity() ? max(1, (int)$object->getQuantity()) : 1;
            $this->_setAttribute('quantity', $quantity, 'int');
        }

        $targetCountry = $this->getConfig()->getTargetCountry($this->getStoreId());

        if ($object->getData('image_url')) {
            $this->_setAttribute('image_link', $object->getData('image_url'), 'url');
        }

        $this->_setAttribute('condition', 'new', 'text');
        $this->_setAttribute('target_country', $targetCountry, 'text');
        $this->_setAttribute('item_language', $this->getConfig()->getCountryInfo($targetCountry, 'language'), 'text');
        // set new 'attribute_values' with removed 'title' and/or 'description' keys to avoid 'duplicate' errors
        $this->setAttributeValues($attributeValues);

        return $this;
    }

    /**
     * Set Google Base Item Attribute
     *
     * @param string $attribute Google Base attribute name
     * @param string $value Google Base attribute value
     * @param string $type Google Base attribute type
     *
     * @return Mage_GoogleBase_Model_Service_Item
     */
    protected function _setAttribute($attribute, $value, $type = 'text')
    {
        $entry = $this->getEntry();
        $gBaseAttribute = $entry->getGbaseAttribute($attribute);
        if (isset($gBaseAttribute[0]) && is_object($gBaseAttribute[0])) {
            $gBaseAttribute[0]->text = $value;
        } else {
            $entry->addGbaseAttribute($attribute, $value, $type);
        }
        return $this;
    }

    /**
     * Custom setter for 'price' attribute
     *
     * @param string $attribute Google Base attribute name
     * @param mixed $value Fload price value
     * @param string $type Google Base attribute type
     *
     * @return Mage_GoogleBase_Model_Service_Item
     */
    protected function _setAttributePrice($attribute, $value, $type = 'text')
    {
        if (!$this->getData('price_assigned')) {
            $targetCountry = $this->getConfig()->getTargetCountry($this->getStoreId());
            $this->_setAttribute(
                $this->getConfig()->getCountryInfo($targetCountry, 'price_attribute_name', $this->getStoreId()),
                sprintf('%.2f', $value),
                'floatUnit'
            );
            $this->setData('price_assigned', true);
        }
    }

    /**
     * Return Google Base Item Attribute Value
     *
     * @param string $attribute Google Base attribute name
     * @return string|null Attribute value
     */
    protected function _getAttributeValue($attribute)
    {
        $entry = $this->getEntry();
        $attributeArr = $entry->getGbaseAttribute($attribute);
        if (is_array($attributeArr) && is_object($attributeArr[0])) {
            return $attributeArr[0]->getText();
        }
        return null;
    }

    /**
     * Return assign item type or default item type
     *
     * @return string Google Base Item Type
     */
    protected function _getItemType()
    {
        return $this->getItemType()
            ? $this->getItemType()
            : $this->getConfig()->getDefaultItemType($this->getStoreId());
    }

    /**
     * Check Item Instance
     *
     * @return void
     */
    protected function _checkItem()
    {
        if (!($this->getItem() instanceof Mage_GoogleBase_Model_Item)) {
            Mage::throwException(Mage::helper('googlebase')->__('Item model is not specified to delete Google Base entry.'));
        }
    }

    /**
     * Prepare Google Base attribute name before save
     *
     * @param string Attribute name
     * @return string Normalized attribute name
     */
    protected function _normalizeString($string)
    {
        return preg_replace('/\s+/', '_', $string);

//        $string = preg_replace('/([^a-z^0-9^_])+/','_',strtolower($string));
//        $string = preg_replace('/_{2,}/','_',$string);
//        return trim($string,'_');
    }

    /**
     * Convert Google Base date format to unix timestamp
     * Ex. 2008-12-08T16:57:23Z -> 2008-12-08 16:57:23
     *
     * @param string Google Base datetime
     * @return int
     */
    public function gBaseDate2DateTime($gBaseDate)
    {
        return Mage::getSingleton('core/date')->date(null, $gBaseDate);
    }
}
