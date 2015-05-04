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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Search Category Model
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Adminhtml_Search_Category extends Varien_Object
{
    /**
     * Load search results
     *
     * @return Mage_XmlConnect_Model_Adminhtml_Search_Category
     */
    public function load()
    {
        $arr = array();

        if (!$this->hasStart() || !$this->hasLimit() || !$this->hasQuery()) {
            $this->setResults($arr);
            return $this;
        }
        /** @var $collection Mage_XmlConnect_Model_Resource_CategorySearch_Collection */
        $collection = Mage::getResourceModel('xmlconnect/categorySearch_collection');
        $collection->addAttributeToSelect('name')->addAttributeToSelect('description')
            ->addSearchFilter($this->getQuery())->setCurPage($this->getStart())->setPageSize($this->getLimit())->load();

        foreach ($collection as $category) {
            $description = Mage::helper('core')->stripTags($category->getDescription());
            $arr[] = array(
                'id'          => 'category/1' . $category->getEntityId(),
                'item_id'     => $category->getId(),
                'type'        => Mage_XmlConnect_Model_ImageAction::ACTION_TYPE_CATEGORY,
                'label'       => Mage::helper('adminhtml')->__('Category'),
                'name'        => $category->getName(),
                'image'       => null,
                'description' => Mage::helper('core/string')->substr($description, 0, 30),
                'url'         => Mage::helper('adminhtml')->getUrl('*/catalog_category/index'),
            );
        }
        $this->setResults($arr);
        return $this;
    }
}
