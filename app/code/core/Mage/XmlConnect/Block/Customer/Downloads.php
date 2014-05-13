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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer Downloadable Products list xml renderer
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Customer_Downloads extends Mage_Downloadable_Block_Customer_Products_List
{
    /**
     * Render downloadable products list xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $items = $this->getItems();

        if (count($items)) {
            /** @var $xmlModel Mage_XmlConnect_Model_Simplexml_Element */
            $xmlModel = Mage::getModel('xmlconnect/simplexml_element', '<downloads></downloads>');

            foreach ($items as $item) {
                $itemXmlObj = $xmlModel->addCustomChild('item');
                $itemXmlObj->addCustomChild('title', $item->getPurchased()->getProductName());
                $itemXmlObj->addCustomChild('link', $this->getDownloadUrl($item), array(
                    'label' => $item->getLinkTitle()
                ));
                $itemXmlObj->addCustomChild('status', Mage::helper('downloadable')->__(ucfirst($item->getStatus())));
                $itemXmlObj->addCustomChild('downloads_limit', $this->getRemainingDownloads($item));
                $itemXmlObj->addCustomChild('date', $this->formatDate($item->getPurchased()->getCreatedAt()));
                $itemXmlObj->addCustomChild('order_id', $item->getPurchased()->getOrderId());
                $itemXmlObj->addCustomChild('real_order_id', $item->getPurchased()->getOrderIncrementId());
            }
        } else {
            Mage::throwException(
                Mage::helper('downloadable')->__('You have not purchased any downloadable products yet.')
            );
        }

        return $xmlModel->asNiceXml();
    }

    /**
     * Return url to download link
     *
     * @param Mage_Downloadable_Model_Link_Purchased_Item $item
     * @return string
     */
    public function getDownloadUrl($item)
    {
        return $this->getUrl('downloadable/download/link', array('id' => $item->getLinkHash(), '_secure' => true));
    }
}
