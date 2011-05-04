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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product review form xml renderer
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Review_Form extends Mage_Core_Block_Template
{
    /**
     * Collection of ratings
     *
     * @var array
     */
    protected $_ratings = null;

    /**
     * Render product review form xml
     *
     * @return string
     */
    protected function _toHtml()
    {
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        $xmlModel = Mage::getModel('xmlconnect/simplexml_element', '<node></node>');

        $firstname = $ratingsXml = '';
        if ($customer->getId()) {
            $firstname = $xmlModel->xmlentities(strip_tags($customer->getFirstname()));
        }

        if ($this->getRatings()) {
            foreach ($this->getRatings() as $rating) {
                $ratingTitle = $xmlModel->xmlentities($rating->getRatingCode());
                $ratingCode = strtolower($rating->getRatingCode());
                $ratingCode = preg_replace('/[\W]+/', '_', $ratingCode);
                $ratingsXml .= '
    <fieldset name="rating_' . $ratingCode . '" title="' . $ratingTitle . '">';
                foreach ($rating->getOptions() as $option) {
                    $ratingsXml .= '
        <field name="ratings[' . $rating->getId() . ']" value="'
                        . $option->getId() . '" required="true" type="radio"/>';
                }
                $ratingsXml .= '
    </fieldset>';
            }
        }

        $xml = <<<EOT
<form name="review_form" method="post">
    <fieldset>
        <field name="nickname" type="text" label="{$this->__('Nickname')}" required="true" value="{$firstname}" />
        <field name="title" type="text" label="{$this->__('Summary of Your Review')}" required="true" />
        <field name="detail" type="text" label="{$this->__('Review')}" required="true" />
    </fieldset>{$ratingsXml}
</form>
EOT;

        return $xml;
    }

    /**
     * Returns collection of ratings
     *
     * @return array | false
     */
    public function getRatings()
    {
        if (is_null($this->_ratings)) {
            $this->_ratings = Mage::getModel('rating/rating')
                ->getResourceCollection()
                ->addEntityFilter('product')
                ->setPositionOrder()
                ->addRatingPerStoreName(Mage::app()->getStore()->getId())
                ->setStoreFilter(Mage::app()->getStore()->getId())
                ->load()
                ->addOptionToItems();

            if (!$this->_ratings->getSize()) {
                $this->_ratings = false;
            }
        }
        return $this->_ratings;
    }
}
