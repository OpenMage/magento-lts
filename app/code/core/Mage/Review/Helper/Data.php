<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Review
 */

/**
 * Default review helper
 *
 * @package    Mage_Review
 */
class Mage_Review_Helper_Data extends Mage_Core_Helper_Abstract
{
    public const XML_REVIEW_GUETS_ALLOW = 'catalog/review/allow_guest';

    protected $_moduleName = 'Mage_Review';

    /**
     * @param string $origDetail
     * @return string
     */
    public function getDetail($origDetail)
    {
        return nl2br(Mage::helper('core/string')->truncate($origDetail, 50));
    }

    /**
     * getDetailHtml return short detail info in HTML
     * @param string $origDetail Full detail info
     * @return string
     */
    public function getDetailHtml($origDetail)
    {
        return nl2br(Mage::helper('core/string')->truncate($this->escapeHtml($origDetail), 50));
    }

    /**
     * @return bool
     */
    public function getIsGuestAllowToWrite()
    {
        return Mage::getStoreConfigFlag(self::XML_REVIEW_GUETS_ALLOW);
    }

    /**
     * Get review statuses with their codes
     *
     * @return array
     */
    public function getReviewStatuses()
    {
        return [
            Mage_Review_Model_Review::STATUS_APPROVED     => $this->__('Approved'),
            Mage_Review_Model_Review::STATUS_PENDING      => $this->__('Pending'),
            Mage_Review_Model_Review::STATUS_NOT_APPROVED => $this->__('Not Approved'),
        ];
    }

    /**
     * Get review statuses as option array
     *
     * @return array
     */
    public function getReviewStatusesOptionArray()
    {
        $result = [];
        foreach ($this->getReviewStatuses() as $k => $v) {
            $result[] = ['value' => $k, 'label' => $v];
        }

        return $result;
    }
}
