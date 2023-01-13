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
 * @package    Mage_Dataflow
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert zend db adapter
 *
 * @category   Mage
 * @package    Mage_Dataflow
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Convert_Adapter_Zend_Db extends Mage_Dataflow_Model_Convert_Adapter_Abstract
{
    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = Zend_Db::factory($this->getVar('adapter', 'Pdo_Mysql'), $this->getVars());
        }
        return $this->_resource;
    }

    public function load()
    {
        return $this;
    }

    public function save()
    {
        return $this;
    }
}
