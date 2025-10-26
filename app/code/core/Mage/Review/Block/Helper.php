<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Review
 */

/**
 * Review helper
 *
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
