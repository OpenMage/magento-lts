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
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Block_Catalog_Review extends Mage_Rss_Block_Abstract
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_review');
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        $newurl = Mage::getUrl('rss/catalog/review');
        $title = Mage::helper('rss')->__('Pending product review(s)');

        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                );
        $rssObj->_addHeader($data);

        $reviewModel = Mage::getModel('review/review');

        $collection = $reviewModel->getProductCollection()
            ->addStatusFilter($reviewModel->getPendingStatus())
            ->addAttributeToSelect('name', 'inner')
            ->setDateOrder();

         Mage::getSingleton('core/resource_iterator')
            ->walk($collection->getSelect(), array(array($this, 'addReviewItemXmlCallback')), array('rssObj'=> $rssObj, 'reviewModel'=> $reviewModel));
        return $rssObj->createRssXml();
    }

    public function addReviewItemXmlCallback($args)
    {
        $rssObj = $args['rssObj'];
        $reviewModel = $args['reviewModel'];
        $row = $args['row'];

        $productUrl = Mage::getUrl('catalog/product/view',array('id'=>$row['entity_id']));
        $reviewUrl = Mage::helper('adminhtml')->getUrl('adminhtml/catalog_product_review/edit/', array('id'=>$row['review_id'],'_secure' => true));
        $storeName = Mage::app()->getStore($row['store_id'])->getName();

        $description = '<p>'.
        $this->__('Product: <a href="%s">%s</a> <br/>',$productUrl,$row['name']).
        $this->__('Summary of review: %s <br/>',$row['title']).
        $this->__('Review: %s <br/>', $row['detail']).
        $this->__('Store: %s <br/>', $storeName ).
        $this->__('click <a href="%s">here</a> to view the review',$reviewUrl).
        '</p>'
        ;
        $data = array(
                'title'         => $this->__('Product: "%s" review By: %s',$row['name'],$row['nickname']),
                'link'          => 'test',
                'description'   => $description,
                );
        $rssObj->_addEntry($data);
    }
}