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
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Block_Catalog_Special extends Mage_Rss_Block_Abstract
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_special_'.$this->getStoreId().'_'.$this->_getCustomerGroupId());
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
         //store id is store view id
        $storeId = $this->_getStoreId();
        $websiteId = Mage::app()->getStore($storeId)->getWebsiteId();

        //customer group id
        $custGroup =   $this->_getCustomerGroupId();

        $product = Mage::getModel('catalog/product');
        $todayDate = $product->getResource()->formatDate(time());

        $rulePriceWhere = "({{table}}.rule_date is null) or ({{table}}.rule_date='$todayDate' and {{table}}.website_id='$websiteId' and {{table}}.customer_group_id='$custGroup')";

        $specials = $product->setStoreId($storeId)->getResourceCollection()
            ->addAttributeToFilter('special_price', array('gt'=>0), 'left')
            ->addAttributeToFilter('special_from_date', array('date'=>true, 'to'=> $todayDate), 'left')
            ->addAttributeToFilter(array(
                array('attribute'=>'special_to_date', 'date'=>true, 'from'=>$todayDate),
                array('attribute'=>'special_to_date', 'is' => new Zend_Db_Expr('null'))
            ), '', 'left')
            ->addAttributeToSort('special_from_date', 'desc')
            ->addAttributeToSelect(array('name', 'short_description', 'description', 'price', 'thumbnail', 'special_to_date'), 'inner')
            ->joinTable('catalogrule/rule_product_price', 'product_id=entity_id', array('rule_price'=>'rule_price', 'rule_start_date'=>'latest_start_date'), $rulePriceWhere, 'left')
        ;

        $rulePriceCollection = Mage::getResourceModel('catalogrule/rule_product_price_collection')
            ->addFieldToFilter('website_id', $websiteId)
            ->addFieldToFilter('customer_group_id', $custGroup)
            ->addFieldToFilter('rule_date', $todayDate)
        ;

        $productIds = $rulePriceCollection->getProductIds();

        if (!empty($productIds)) {
            $specials->getSelect()->orWhere('e.entity_id in ('.implode(',',$productIds).')');
        }

        $specials->setVisibility(Mage::getSingleton('catalog/product_visibility')->getVisibleInCatalogIds());

        $newurl = Mage::getUrl('rss/catalog/special/store_id/' . $storeId);
        $title = Mage::helper('rss')->__('%s - Special Products', Mage::app()->getStore()->getFrontendName());
        $lang = Mage::getStoreConfig('general/locale/code');

        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                'language'    => $lang
                );
        $rssObj->_addHeader($data);

        $results = array();
        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        Mage::getSingleton('core/resource_iterator')
            ->walk($specials->getSelect(), array(array($this, 'addSpecialXmlCallback')), array('rssObj'=> $rssObj, 'results'=> &$results));

        if(sizeof($results)>0){
            usort($results, array(&$this, 'sortByStartDate'));
            foreach($results as $result){
                $product->setData($result);

                $description = '<table><tr>'.
                    '<td><a href="'.$product->getProductUrl().'"><img src="'. $this->helper('catalog/image')->init($product, 'thumbnail')->resize(75, 75) .'" border="0" align="left" height="75" width="75"></a></td>'.
                    '<td  style="text-decoration:none;">'.$product->getDescription();

                if ($product->getAllowedPriceInRss()) {
                    $specialPrice = ($result['use_special'] ? $result['special_price'] : $result['rule_price']);
                    $description .= '<p> Price:'.Mage::helper('core')->currency($product->getPrice()).
                        ' Special Price:'. Mage::helper('core')->currency($specialPrice).
                    ($result['use_special'] && $result['special_to_date'] ? '<br/> Special Expires on: '.$this->formatDate($result['special_to_date'], Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM) : '').
                    '</p>';
                }

                $description .= '</td></tr></table>';

                $data = array(
                        'title'         => $product->getName(),
                        'link'          => $product->getProductUrl(),
                        'description'   => $description
                    );
                $rssObj->_addEntry($data);
            }
        }
        return $rssObj->createRssXml();
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     */
    public function addSpecialXmlCallback($args)
    {
        /*
         * RSS state object
         */
        $product = new Varien_Object();
        //Product is allowed for RSS initially
        $product->setAllowedInRss(true);
        $args['product'] = $product;

        Mage::dispatchEvent('rss_catalog_special_xml_callback', $args);

        if (!$product->getAllowedInRss()) {
            //Skip adding product to RSS
            return;
        }

       $row = $args['row'];

       if ($product->getAllowedPriceInRss()) {
           $specialPrice = $row['special_price'];
           $rulePrice = $row['rule_price'];
           if (!$rulePrice || ($rulePrice && $specialPrice && $specialPrice<=$rulePrice)) {
               $row['start_date'] = $row['special_from_date'];
               $row['use_special'] = true;
           } else {
               $row['start_date'] = $row['rule_start_date'];
               $row['use_special'] = false;
           }
           $row['allowed_price_in_rss'] = true;
       } else {
           $row['start_date'] = null;
           $row['allowed_price_in_rss'] = false;
       }
       $args['results'][] = $row;
    }


    /**
     * Function for comparing two items in collection
     *
     * @param   Varien_Object $item1
     * @param   Varien_Object $item2
     * @return  boolean
     */
    public function sortByStartDate($a, $b)
    {
        return $a['start_date']>$b['start_date'] ? -1 : ($a['start_date']<$b['start_date'] ? 1 : 0);
    }
}
