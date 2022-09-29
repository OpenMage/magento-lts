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
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment CC Types Source Model
 *
 * @category   Mage
 * @package    Mage_Payment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Payment_Model_Source_Cctype
{
    /**
     * Allowed CC types
     *
     * @var array
     */
    protected $_allowedTypes = [];

    /**
     * Return allowed cc types for current method
     *
     * @return array
     */
    public function getAllowedTypes()
    {
        return $this->_allowedTypes;
    }

    /**
     * Setter for allowed types
     *
     * @param array $values
     * @return $this
     */
    public function setAllowedTypes(array $values)
    {
        $this->_allowedTypes = $values;
        return $this;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        /**
         * making filter by allowed cards
         */
        $allowed = $this->getAllowedTypes();
        $options = [];

        foreach (Mage::getSingleton('payment/config')->getCcTypes() as $code => $name) {
            if (in_array($code, $allowed) || !count($allowed)) {
                $options[] = [
                   'value' => $code,
                   'label' => $name
                ];
            }
        }

        return $options;
    }
}
