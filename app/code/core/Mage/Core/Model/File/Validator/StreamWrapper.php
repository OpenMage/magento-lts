<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
    protected $_allowedStreamWrappers = array();

    /**
     * Mage_Core_Model_File_Validator_StreamWrapper constructor.
     *
     * @param array $allowedStreamWrappers
     */
    public function __construct($allowedStreamWrappers = array())
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
