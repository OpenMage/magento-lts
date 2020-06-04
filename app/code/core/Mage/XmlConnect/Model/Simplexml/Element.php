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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect fixed Varien SimpleXML Element class
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Simplexml_Element extends Varien_Simplexml_Element
{
    /**
     * Appends $source to current node
     *
     * @param Mage_XmlConnect_Model_Simplexml_Element $source
     * @return $this
     */
    public function appendChild($source)
    {
        if (sizeof($source->children())) {
            $name = $source->getName();
            $child = $this->addChild($name);
        } else {
            $child = $this->addChild($source->getName(), $this->xmlentities($source));
        }
        $child->setParent($this);

        $attributes = $source->attributes();
        foreach ($attributes as $key=>$value) {
            $child->addAttribute($key, $this->xmlAttribute($value));
        }

        foreach ($source->children() as $sourceChild) {
            $child->appendChild($sourceChild);
        }
        return $this;
    }

    /**
     * Escape xml entities
     *
     * @param mixed $data
     * @param bool $stripTags
     * @param array $allowedTags
     * @return mixed
     */
    public function escapeXml($data, $stripTags = true, $allowedTags = null)
    {
        if (is_array($data)) {
            $result = array();
            foreach ($data as $item) {
                if ($stripTags) {
                    $item = Mage::helper('core')->stripTags($item, $allowedTags);
                }
                $result[] = $this->xmlentities($item);
            }
        } else {
            if (is_null($data)) {
                $data = $this;
            }
            $data = (string)$data;

            if ($stripTags) {
                $data = Mage::helper('core')->stripTags($data, $allowedTags);
            }
            $result = $this->xmlentities($data);
        }
        return $result;
    }

    /**
     * Converts meaningful xml character (") to xml attribute specification
     *
     * @param string $value
     * @param bool $stripTags
     * @return string|Mage_XmlConnect_Model_Simplexml_Element|null
     */
    public function xmlAttribute($value = null, $stripTags = true)
    {
        if (is_null($value)) {
            $value = $this;
        }
        $value = (string)$value;

        if ($stripTags) {
            $value = Mage::helper('core')->stripTags($value);
        }
        $value = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $value);
        return $value;
    }

    /**
     * Add field to fieldset
     *
     * @param string $elementName
     * @param string $elementType
     * @param array $config
     * @return $this
     */
    public function addField($elementName, $elementType, array $config)
    {
        $newField = $this->addChild('field');
        $newField->addAttribute('name', $this->xmlAttribute($elementName));
        $newField->addAttribute('type', $this->xmlAttribute($elementType));
        foreach ($config as $key => $val) {
            $newField->addAttribute($key, $this->xmlAttribute($val));
        }
        return $newField;
    }

    /**
     * Add custom field to SimpleXML element
     *
     * @param string $childName
     * @param string $value
     * @param array $config
     * @return $this
     */
    public function addCustomChild($childName, $value = null, $config = null)
    {
        $customFiled = $this->addChild($childName, $this->xmlentities($value));

        if (is_array($config)) {
            foreach ($config as $key => $val) {
                $customFiled->addAttribute($key, $this->xmlAttribute($val));
            }
        }
        return $customFiled;
    }
}
