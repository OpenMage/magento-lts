<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Review
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review helper
 *
 * @category   Mage
 * @package    Mage_Review
 *
 * @method $this setDisplayIfEmpty(bool $value)
 * @method Mage_Catalog_Model_Product getProduct()
 * @method $this setProduct(Mage_Catalog_Model_Product $value)
 */
class Mage_Review_Block_Helper extends Mage_Core_Block_Template
{
    protected $_availableTemplates = [
        'default' => 'review/helper/summary.phtml',
        'short'   => 'review/helper/summary_short.phtml',
    ];

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param string $templateType
     * @param bool $displayIfNoReviews
     * @return string
     * @throws Mage_Core_Model_Store_Exception
     */
    public function getSummaryHtml($product, $templateType, $displayIfNoReviews)
    {
        // pick template among available
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = 'default';
        }
        $this->setTemplate($this->_availableTemplates[$templateType]);

        $this->setDisplayIfEmpty($displayIfNoReviews);

        if (!$product->getRatingSummary()) {
            Mage::getModel('review/review')
               ->getEntitySummary($product, Mage::app()->getStore()->getId());
        }
        $this->setProduct($product);

        return $this->toHtml();
    }

    /**
     * @return array
     */
    public function getRatingSummary()
    {
        return $this->getProduct()->getRatingSummary()->getRatingSummary();
    }

    /**
     * @return int
     */
    public function getReviewsCount()
    {
        return $this->getProduct()->getRatingSummary()->getReviewsCount();
    }

    /**
     * @return string
     */
    public function getReviewsUrl()
    {
        return Mage::getUrl('review/product/list', [
            'id'        => $this->getProduct()->getId(),
            'category'  => $this->getProduct()->getCategoryId(),
        ]);
    }

    /**
     * Add an available template by type
     *
     * It should be called before getSummaryHtml()
     *
     * @param string $type
     * @param string $template
     */
    public function addTemplate($type, $template)
    {
        $this->_availableTemplates[$type] = $template;
    }
}
