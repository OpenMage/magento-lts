<?php
/**
 * ProductAlert email back in stock grid
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_ProductAlert
 */
class Mage_ProductAlert_Block_Email_Stock extends Mage_ProductAlert_Block_Email_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('email/productalert/stock.phtml');
    }

    /**
     * Retrieve unsubscribe url for product
     *
     * @param int $productId
     * @return string
     */
    public function getProductUnsubscribeUrl($productId)
    {
        $params = $this->_getUrlParams();
        $params['product'] = $productId;
        return $this->getUrl('productalert/unsubscribe/stock', $params);
    }

    /**
     * Retrieve unsubscribe url for all products
     *
     * @return string
     */
    public function getUnsubscribeUrl()
    {
        return $this->getUrl('productalert/unsubscribe/stockAll', $this->_getUrlParams());
    }
}
