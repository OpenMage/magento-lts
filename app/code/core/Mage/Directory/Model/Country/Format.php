<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Directory
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Directory country format model
 *
 * @category   Mage
 * @package    Mage_Directory
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
