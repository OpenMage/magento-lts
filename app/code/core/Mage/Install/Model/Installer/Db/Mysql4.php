<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Install
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mysql resource data model
 *
 * @category   Mage
 * @package    Mage_Install
 */
class Mage_Install_Model_Installer_Db_Mysql4 extends Mage_Install_Model_Installer_Db_Abstract
{
    /**
     * Retrieve DB server version
     *
     * @return string (string version number | 'undefined')
     */
    public function getVersion()
    {
        $version  = $this->_getConnection()
            ->fetchOne('SELECT VERSION()');
        $version    = $version ? $version : 'undefined';
        $match = [];
        if (preg_match("#^([0-9\.]+)#", $version, $match)) {
            $version = $match[0];
        }
        return $version;
    }

    /**
     * Check InnoDB support
     *
     * @return bool
     */
    public function supportEngine()
    {
        $variables  = $this->_getConnection()
            ->fetchPairs('SHOW ENGINES');
        return isset($variables['InnoDB']) && ($variables['InnoDB'] == 'DEFAULT' || $variables['InnoDB'] == 'YES');
    }
}
