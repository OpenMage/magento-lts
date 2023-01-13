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
 * @package    Mage_Rule
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract Rule entity resource model
 *
 * @category   Mage
 * @package    Mage_Rule
 * @author     Magento Core Team <core@magentocommerce.com>
 * @deprecated since 1.7.0.0 use Mage_Rule_Model_Resource_Abstract instead
 */
class Mage_Rule_Model_Resource_Rule extends Mage_Rule_Model_Resource_Abstract
{
    protected function _construct()
    {
        $this->_init('rule/rule', 'rule_id');
    }
}
