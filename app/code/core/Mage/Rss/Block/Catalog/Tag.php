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
class Mage_Rss_Block_Catalog_Tag extends Mage_Rss_Block_Abstract
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_tag_'.$this->getStoreId());
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        //store id is store view id
        $storeId = $this->_getStoreId();
        $tagModel = Mage::registry('tag_model');
        $newurl = Mage::getUrl('rss/catalog/new');
        $title = Mage::helper('rss')->__('Products tagged with %s', $tagModel->getName());
        $lang = Mage::getStoreConfig('general/locale/code');

        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
            'description' => $title,
            'link'        => $newurl,
            'charset'     => 'UTF-8',
            'language'    => $lang
        );
        $rssObj->_addHeader($data);

        $_collection = $tagModel->getEntityCollection()
            ->addTagFilter($tagModel->getId())
            ->addStoreFilter($storeId);

        $product = Mage::getModel('catalog/product');

        Mage::getSingleton('core/resource_iterator')
                ->walk($_collection->getSelect(), array(array($this, 'addTaggedItemXml')), array('rssObj'=> $rssObj, 'product'=>$product));

        return $rssObj->createRssXml();
    }

    public function addTaggedItemXml($args)
    {
        $product = $args['product'];
        $product->unsetData()->load($args['row']['entity_id']);
        $description = '<table><tr>'.
        '<td><a href="'.$product->getProductUrl().'"><img src="'.$product->getThumbnailUrl().'" border="0" align="left" height="75" width="75"></a></td>'.
        '<td  style="text-decoration:none;">'.$product->getDescription().
        '<p> Price:'.Mage::helper('core')->currency($product->getFinalPrice()).'</p>'.
        '</td>'.
        '</tr></table>';
        $rssObj = $args['rssObj'];
        $data = array(
                'title'         => $product->getName(),
                'link'          => $product->getProductUrl(),
                'description'   => $description,

                );
        $rssObj->_addEntry($data);
    }
}