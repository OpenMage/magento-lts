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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer country attribute source
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Entity_Address_Attribute_Source_Country extends Mage_Customer_Model_Resource_Address_Attribute_Source_Country
{
    /**
     * Factory instance
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_factory;

    /**
     * Constructor for Mage_Customer_Model_Entity_Address_Attribute_Source_Country
     *
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->_factory = !empty($args['factory']) ? $args['factory'] : Mage::getSingleton('core/factory');
    }
    /**
     * Retrieve all options
     *
     * @param bool $withEmpty       Argument has no effect, included for PHP 7.2 method signature compatibility
     * @param bool $defaultValues   Argument has no effect, included for PHP 7.2 method signature compatibility
     * @return array
     */
    public function getAllOptions($withEmpty = true, $defaultValues = false)
    {
        if (!$this->_options) {
            $this->_options = $this->_factory->getResourceModel('directory/country_collection')
                ->loadByStore($this->getAttribute()->getStoreId())->toOptionArray();
        }
        return $this->_options;
    }
}
