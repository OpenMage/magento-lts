<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Wishlist
 */

/**
 * Wishlist customer sharing block
 *
 * @package    Mage_Wishlist
 */
class Mage_Wishlist_Block_Customer_Sharing extends Mage_Core_Block_Template
{
    /**
     * Entered Data cache
     *
     * @var null|array
     */
    protected $_enteredData = null;

    /**
     * Prepare Global Layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $headBlock = $this->getLayout()->getBlock('head');
        if ($headBlock) {
            $headBlock->setTitle($this->__('Wishlist Sharing'));
        }

        return $this;
    }

    /**
     * Retrieve Send Form Action URL
     *
     * @return string
     */
    public function getSendUrl()
    {
        return $this->getUrl('*/*/send');
    }

    /**
     * Retrieve Entered Data by key
     *
     * @param  string $key
     * @return mixed
     */
    public function getEnteredData($key)
    {
        if (is_null($this->_enteredData)) {
            $this->_enteredData = Mage::getSingleton('wishlist/session')
                ->getData('sharing_form', true);
        }

        if (!$this->_enteredData || !isset($this->_enteredData[$key])) {
            return null;
        }

        return $this->escapeHtml($this->_enteredData[$key]);
    }

    /**
     * Retrieve back button url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/index');
    }
}
