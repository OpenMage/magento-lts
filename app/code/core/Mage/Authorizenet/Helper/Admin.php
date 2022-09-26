<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Authorizenet Admin Data Helper
 *
 * @category   Mage
 * @package    Mage_Authorizenet
 * @author     Magento Core Team <core@magentocommerce.com>
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

        if ($controller === "sales_order_create" || $controller === "sales_order_edit") {
            $route['action'] = 'save';
            $route['controller'] = 'sales_order_create';
            $route['module'] = 'admin';
        }

        return $route;
    }
}
