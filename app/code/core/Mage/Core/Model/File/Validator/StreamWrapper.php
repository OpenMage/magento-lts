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
 * @category    Mage
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Validator for check is stream wrapper allowed
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_File_Validator_StreamWrapper
{
    /**
     * Allowed stream wrappers
     *
     * @var array
     */
    protected $_allowedStreamWrappers = [];

    /**
     * Mage_Core_Model_File_Validator_StreamWrapper constructor.
     *
     * @param array $allowedStreamWrappers
     */
    public function __construct($allowedStreamWrappers = [])
    {
        $this->_allowedStreamWrappers = $allowedStreamWrappers;
    }

    /**
     * Validation callback for checking is stream wrapper allowed
     *
     * @param  string $filePath Path to file
     * @return boolean
     */
    public function validate($filePath)
    {
        if (($pos = strpos($filePath, '://')) > 0) {
            $wrapper = substr($filePath, 0, $pos);
            if (!in_array($wrapper, $this->_allowedStreamWrappers)) {
                 return false;
            }
        }
        return true;
    }
}
