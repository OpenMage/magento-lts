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
 * @category   Magento
 * @package    Magento_Db
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Magento_Db_Object_Interface
 *
 * @category   Magento
 * @package    Magento_Db
 * @author     Magento Core Team <core@magentocommerce.com>
 */
interface Magento_Db_Object_Interface
{
    /**
     * Describe database object
     *
     * @return array
     */
    public function describe();

    /**
     * Drop database object
     */
    public function drop();

    /**
     * Check that database object is exist
     */
    public function isExists();
}
