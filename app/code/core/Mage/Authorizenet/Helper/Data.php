<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Authorizenet
 */

/**
 * Authorizenet Data Helper
 *
 * @package    Mage_Authorizenet
 */
class Mage_Authorizenet_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_moduleName = 'Mage_Authorizenet';

    /**
     * Return URL for admin area
     *
     * @param  string $route
     * @param  array  $params
     * @return string
     */
    public function getAdminUrl($route, $params)
    {
        return Mage::getModel('adminhtml/url')->getUrl($route, $params);
    }

    /**
     * Set secure url checkout is secure for current store.
     *
     * @param  string $route
     * @param  array  $params
     * @return string
     */
    protected function _getUrl($route, $params = [])
    {
        $params['_type'] = Mage_Core_Model_Store::URL_TYPE_LINK;
        if (isset($params['is_secure'])) {
            $params['_secure'] = (bool) $params['is_secure'];
        } elseif (Mage::app()->getStore()->isCurrentlySecure()) {
            $params['_secure'] = true;
        }

        return parent::_getUrl($route, $params);
    }

    /**
     * Retrieve save order url params
     *
     * @param  string $controller
     * @return array
     */
    public function getSaveOrderUrlParams($controller)
    {
        $route = [];
        if ($controller === 'onepage') {
            $route['action'] = 'saveOrder';
            $route['controller'] = 'onepage';
            $route['module'] = 'checkout';
        }

        return $route;
    }

    /**
     * Retrieve redirect iframe url
     * @param  array  $params
     * @return string
     */
    public function getRedirectIframeUrl($params)
    {
        return $this->_getUrl('authorizenet/directpost_payment/redirect', $params);
    }

    /**
     * Retrieve place order url on front
     *
     * @return string
     */
    public function getPlaceOrderFrontUrl()
    {
        $params = [Mage_Core_Model_Url::FORM_KEY => Mage::getSingleton('core/session')->getFormKey()];
        return $this->_getUrl('authorizenet/directpost_payment/place', $params);
    }

    /**
     * Retrieve place order url in admin
     *
     * @return string
     */
    public function getPlaceOrderAdminUrl()
    {
        return $this->getAdminUrl('*/authorizenet_directpost_payment/place', []);
    }

    /**
     * Retrieve place order url
     *
     * @param  array  $params
     * @return string
     */
    public function getSuccessOrderUrl($params)
    {
        return $this->_getUrl('checkout/onepage/success', []);
    }

    /**
     * Get controller name
     *
     * @return string
     */
    public function getControllerName()
    {
        return Mage::app()->getFrontController()->getRequest()->getControllerName();
    }

    /**
     * Update all child and parent order's edit increment numbers.
     * Needed for Admin area.
     */
    public function updateOrderEditIncrements(Mage_Sales_Model_Order $order)
    {
        if ($order->getId() && $order->getOriginalIncrementId()) {
            $collection = $order->getCollection();
            $quotedIncrId = $collection->getConnection()->quote($order->getOriginalIncrementId());
            $collection->getSelect()->where(
                "original_increment_id = {$quotedIncrId} OR increment_id = {$quotedIncrId}",
            );

            foreach ($collection as $orderToUpdate) {
                $orderToUpdate->setEditIncrement($order->getEditIncrement());
                $orderToUpdate->save();
            }
        }
    }
}
