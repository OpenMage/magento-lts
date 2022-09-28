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
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Save administrators group role and rules
 */
$admGroupRole = Mage::getModel('admin/role')->setData([
    'parent_id'     => 0,
    'tree_level'    => 1,
    'sort_order'    => 1,
    'role_type'     => 'G',
    'user_id'       => 0,
    'role_name'     => 'Administrators'
])
    ->save();

Mage::getModel('admin/rules')->setData([
    'role_id'       => $admGroupRole->getId(),
    'resource_id'   => 'all',
    'privileges'    => null,
    'assert_id'     => 0,
    'role_type'     => 'G',
    'permission'    => 'allow'
])
    ->save();
