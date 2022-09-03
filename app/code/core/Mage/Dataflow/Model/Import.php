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
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * DataFlow Import Model
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method Mage_Dataflow_Model_Resource_Import _getResource()
 * @method Mage_Dataflow_Model_Resource_Import getResource()
 * @method int getSessionId()
 * @method Mage_Dataflow_Model_Import setSessionId(int $value)
 * @method int getSerialNumber()
 * @method Mage_Dataflow_Model_Import setSerialNumber(int $value)
 * @method string getValue()
 * @method Mage_Dataflow_Model_Import setValue(string $value)
 * @method int getStatus()
 * @method Mage_Dataflow_Model_Import setStatus(int $value)
 */
class Mage_Dataflow_Model_Import extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('dataflow/import');
    }
}
