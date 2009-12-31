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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Module configuration changes model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oscommerce_Model_Config extends Mage_Core_Model_Config_Base
{
    public function __construct()
    {
        parent::__construct(Mage::getConfig()->getNode('global'));
    }

    public function initForeignConnection($data)
    {
        $connectionNode = $this->getNode('resources/oscommerce_foreign/connection');
        if ($connectionNode) {
            $connectionNode->addChild('host', isset($data['host']) ? $data['host'] : '');
            $connectionNode->addChild('username', isset($data['db_user']) ? $data['db_user'] : '');
            $connectionNode->addChild('password', isset($data['db_password']) ? $data['db_password'] : '');
            $connectionNode->addChild('dbname', isset($data['db_name']) ? $data['db_name'] : '');
        }
    }
}
