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
 * @package    Mage_Backup
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Class to work with backups
 *
 * @category   Mage
 * @package    Mage_Backup
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backup
{
    /**
     * List of supported a backup types
     *
     * @var array
     */
    protected static $_allowedBackupTypes = ['db', 'snapshot', 'filesystem', 'media', 'nomedia'];

    /**
     * get Backup Instance By File Name
     *
     * @param  string $type
     * @return Mage_Backup_Db|Mage_Backup_Interface
     */
    public static function getBackupInstance($type)
    {
        $class = 'Mage_Backup_' . ucfirst($type);

        if (!in_array($type, self::$_allowedBackupTypes) || !class_exists($class, true)) {
            throw new Mage_Exception('Current implementation not supported this type (' . $type . ') of backup.');
        }

        return new $class();
    }
}
