<?php
/**
 * Magento
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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Log
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Logging level backend source model
 *
 * @category    Mage
 * @package     Mage_Log
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Log_Model_Adminhtml_System_Config_Source_Loglevel
{
    /**
     * Don't log anything
     */
    const LOG_LEVEL_NONE = 0;

    /**
     * All possible logs enabled
     */
    const LOG_LEVEL_ALL = 1;

    /**
     * Logs only visitors, needs for working compare products and customer segment's related functionality
     * (eg. shopping cart discount for segments with not logged in customers)
     */
    const LOG_LEVEL_VISITORS = 2;

    /**
     * @var Mage_Log_Helper_Data
     */
    protected $_helper;

    public function __construct(array $data = array())
    {
        $this->_helper = !empty($data['helper']) ? $data['helper'] : Mage::helper('log');
    }

    public function toOptionArray()
    {
        $options = array(
            array(
                'label' => $this->_helper->__('Yes'),
                'value' => self::LOG_LEVEL_ALL,
            ),
            array(
                'label' => $this->_helper->__('No'),
                'value' => self::LOG_LEVEL_NONE,
            ),
            array(
                'label' => $this->_helper->__('Visitors only'),
                'value' => self::LOG_LEVEL_VISITORS,
            ),
        );

        return $options;
    }
}
