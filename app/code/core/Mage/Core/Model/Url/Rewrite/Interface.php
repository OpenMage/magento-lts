<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Url rewrite interface
 *
 * @category   Mage
 * @package    Mage_Core
 */
interface Mage_Core_Model_Url_Rewrite_Interface
{
    /**
     * Load rewrite information for request
     *
     * @param array|string $path
     * @return Mage_Core_Model_Url_Rewrite_Interface
     */
    public function loadByRequestPath($path);
}
