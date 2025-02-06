<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Admin
 */

/**
 * Admin Variable Helper
 *
 * @category   Mage
 * @package    Mage_Admin
 */
class Mage_Admin_Helper_Variable
{
    /**
     * Paths cache
     *
     * @var array
     */
    protected $_allowedPaths;

    public function __construct()
    {
        $this->_allowedPaths = Mage::getResourceModel('admin/variable')->getAllowedPaths();
    }

    /**
     * @param string $path
     * @return bool
     */
    public function isPathAllowed($path)
    {
        return isset($this->_allowedPaths[$path]);
    }
}
