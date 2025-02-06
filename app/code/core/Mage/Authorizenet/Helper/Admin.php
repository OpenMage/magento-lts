<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 */

/**
 * Authorizenet Admin Data Helper
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 */
class Mage_Authorizenet_Helper_Admin extends Mage_Authorizenet_Helper_Data
{
    /**
     * Retrieve place order url
     * @param array $params
     * @return string
     */
    public function getSuccessOrderUrl($params)
    {
        $url = parent::getSuccessOrderUrl($params);

        if ($params['controller_action_name'] === 'sales_order_create'
            || $params['controller_action_name'] === 'sales_order_edit'
        ) {
            /** @var Mage_Sales_Model_Order $order */
            $order = Mage::getModel('sales/order');
            $order->loadByIncrementId($params['x_invoice_num']);

            $url = $this->getAdminUrl('adminhtml/sales_order/view', ['order_id' => $order->getId()]);
        }

        return $url;
    }

    /**
     * Retrieve save order url params
     *
     * @param string $controller
     * @return array
     */
    public function getSaveOrderUrlParams($controller)
    {
        $route = parent::getSaveOrderUrlParams($controller);

        if ($controller === 'sales_order_create' || $controller === 'sales_order_edit') {
            $route['action'] = 'save';
            $route['controller'] = 'sales_order_create';
            $route['module'] = 'admin';
        }

        return $route;
    }
}
