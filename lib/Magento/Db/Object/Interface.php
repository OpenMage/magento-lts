<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Magento
 * @package    Magento_Db
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Magento_Db_Object_Interface
 *
 * @category   Magento
 * @package    Magento_Db
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
