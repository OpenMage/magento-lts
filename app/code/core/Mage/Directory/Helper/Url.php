<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory URL helper
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Directory_Helper_Url extends Mage_Core_Helper_Url
{
    protected $_moduleName = 'Mage_Directory';

    /**
     * Retrieve switch currency url
     *
     * @param array $params Additional url params
     * @return string
     */
    public function getSwitchCurrencyUrl($params = [])
    {
        $params = is_array($params) ? $params : [];

        if ($this->_getRequest()->getAlias('rewrite_request_path')) {
            $url = Mage::app()->getStore()->getBaseUrl() . $this->_getRequest()->getAlias('rewrite_request_path');
        } else {
            $url = $this->getCurrentUrl();
        }
        $params[Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED] = Mage::helper('core')->urlEncode($url);

        return $this->_getUrl('directory/currency/switch', $params);
    }

    public function getLoadRegionsUrl()
    {
    }
}
