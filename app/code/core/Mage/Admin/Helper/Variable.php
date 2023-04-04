<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Admin
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Admin Variable Helper
 *
 * @category   Mage
 * @package    Mage_Admin
 * @author     Magento Core Team <core@magentocommerce.com>
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
