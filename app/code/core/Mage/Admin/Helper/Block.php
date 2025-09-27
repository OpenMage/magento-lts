<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Admin
 */

/**
 * Admin Block Helper
 *
 * @package    Mage_Admin
 */
class Mage_Admin_Helper_Block
{
    /**
     * Types cache
     *
     * @var array
     */
    protected $_allowedTypes;

    public function __construct()
    {
        $this->_allowedTypes = Mage::getResourceModel('admin/block')->getAllowedTypes();
    }

    /**
     * @param string $type
     * @return bool
     */
    public function isTypeAllowed($type)
    {
        return isset($this->_allowedTypes[$type]);
    }

    /**
     *  Get disallowed names for block
     *
     * @return array
     */
    public function getDisallowedBlockNames()
    {
        return Mage::getResourceModel('admin/block')->getDisallowedBlockNames();
    }
}
