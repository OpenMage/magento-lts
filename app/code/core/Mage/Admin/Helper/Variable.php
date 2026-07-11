<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Admin Variable Helper
 *
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
     * @param  string $path
     * @return bool
     */
    public function isPathAllowed($path)
    {
        return isset($this->_allowedPaths[$path]);
    }
}
