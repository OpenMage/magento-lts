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
class Mage_Rss_Block_Wishlist extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        $descrpt = Mage::helper('core')->urlDecode($this->getRequest()->getParam('data'));
        $data = explode(',',$descrpt);
        $cid = (int)$data[0];

        $rssObj = Mage::getModel('rss/rss');

        if ($cid) {
            $customer = Mage::getModel('customer/customer')->load($cid);
            if ($customer && $customer->getId()) {

                $wishlist = Mage::getModel('wishlist/wishlist')
                ->loadByCustomer($customer, true);

                $newurl = Mage::getUrl('wishlist/shared/index',array('code'=>$wishlist->getSharingCode()));
                $title = Mage::helper('rss')->__('%s\'s Wishlist',$customer->getName());
                $lang = Mage::getStoreConfig('general/locale/code');

                $data = array('title' => $title,
                    'description' => $title,
                    'link'        => $newurl,
                    'charset'     => 'UTF-8',
                    'language'    => $lang
                );
                $rssObj->_addHeader($data);

                $collection = $wishlist->getProductCollection()
                            ->addAttributeToSelect('url_key')
                            ->addAttributeToSelect('name')
                            ->addAttributeToSelect('price')
                            ->addAttributeToSelect('thumbnail')
                            ->addAttributeToFilter('store_id', array('in'=> $wishlist->getSharedStoreIds()))
                            ->load();

                $product = Mage::getModel('catalog/product');
                foreach($collection as $item){
                    $product->unsetData()->load($item->getProductId());
                    $description = '<table><tr>'.
                        '<td><a href="'.$item->getProductUrl().'"><img src="'.$item->getThumbnailUrl().'" border="0" align="left" height="75" width="75"></a></td>'.
                        '<td  style="text-decoration:none;">'.
                        $product->getDescription().
                        '<p> Price:'.Mage::helper('core')->currency($product->getPrice()).
                        ($product->getPrice() != $product->getFinalPrice() ? ' Special Price:'. Mage::helper('core')->currency($product->getFinalPrice()) : '').
                        ($item->getDescription() && $item->getDescription() != Mage::helper('wishlist')->defaultCommentString() ? '<p>Comment: '.$item->getDescription().'<p>' : '').
                        '</td>'.
                        '</tr></table>';
                    $data = array(
                        'title'         => $product->getName(),
                        'link'          => $product->getProductUrl(),
                        'description'   => $description,
                        );
                    $rssObj->_addEntry($data);
                }

            }

        } else {
             $data = array('title' => Mage::helper('rss')->__('Cannot retrieve the wishlist'),
                    'description' => Mage::helper('rss')->__('Cannot retrieve the wishlist'),
                    'link'        => Mage::getUrl(),
                    'charset'     => 'UTF-8',
                );
                $rssObj->_addHeader($data);
        }
        return $rssObj->createRssXml();
    }

}