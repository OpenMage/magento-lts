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
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory country format model
 *
 * @category   Mage
 * @package    Mage_Directory
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Directory_Model_Resource_Country_Format _getResource()
 * @method Mage_Directory_Model_Resource_Country_Format getResource()
 * @method Mage_Directory_Model_Resource_Country_Format_Collection getCollection()
 * @method string getCountryId()
 * @method $this setCountryId(string $value)
 * @method string getType()
 * @method $this setType(string $value)
 * @method string getFormat()
 * @method $this setFormat(string $value)
 */
class Mage_Directory_Model_Country_Format extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('directory/country_format');
    }
}
