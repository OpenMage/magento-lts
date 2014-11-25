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
 * @copyright  Copyright (c) 2006-2014 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Filters xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Catalog_Filters extends Mage_XmlConnect_Block_Catalog
{
    /**
     * Render filters list xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $categoryId         = $this->getRequest()->getParam('category_id', null);
        /** @var $categoryXmlObj Mage_XmlConnect_Model_Simplexml_Element */
        $categoryXmlObj     = Mage::getModel('xmlconnect/simplexml_element', '<category></category>');
        $filtersCollection  = Mage::getResourceModel('xmlconnect/filter_collection')->setCategoryId($categoryId);

        $filtersXmlObj = $categoryXmlObj->addChild('filters');
        foreach ($filtersCollection->getItems() as $item) {
            if (!sizeof($item->getValues())) {
                continue;
            }
            $itemXmlObj = $filtersXmlObj->addChild('item');
            $itemXmlObj->addChild('name', $categoryXmlObj->escapeXml($item->getName()));
            $itemXmlObj->addChild('code', $categoryXmlObj->escapeXml($item->getCode()));

            $valuesXmlObj = $itemXmlObj->addChild('values');
            foreach ($item->getValues() as $value) {
                $valueXmlObj = $valuesXmlObj->addChild('value');
                $valueXmlObj->addChild('id', $categoryXmlObj->escapeXml($value->getValueString()));
                $valueXmlObj->addChild('label', $categoryXmlObj->escapeXml($value->getLabel()));
                $valueXmlObj->addChild('count', (int)$value->getProductsCount());
            }
        }
        $categoryXmlObj->appendChild($this->getProductSortFieldsXmlObject());

        return $categoryXmlObj->asNiceXml();
    }
}
