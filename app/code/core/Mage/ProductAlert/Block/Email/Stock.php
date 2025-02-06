<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_ProductAlert
 */

/**
 * ProductAlert email back in stock grid
 *
 * @category   Mage
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
