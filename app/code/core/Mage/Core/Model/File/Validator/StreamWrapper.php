<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Validator for check is stream wrapper allowed
 *
 * @category   Mage
 * @package    Mage_Core
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
     * @return bool
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
