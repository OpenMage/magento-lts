<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Synchronize process status flag class
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_File_Storage_Flag extends Mage_Core_Model_Flag
{
    /**
     * There was no synchronization
     */
    public const STATE_INACTIVE    = 0;
    /**
     * Synchronize process is active
     */
    public const STATE_RUNNING     = 1;
    /**
     * Synchronization finished
     */
    public const STATE_FINISHED    = 2;
    /**
     * Synchronization finished and notify message was formed
     */
    public const STATE_NOTIFIED    = 3;

    /**
     * Flag time to life in seconds
     */
    public const FLAG_TTL          = 300;

    /**
     * Synchronize flag code
     *
     * @var string
     */
    protected $_flagCode    = 'synchronize';

    /**
     * Pass error to flag
     *
     * @return $this
     */
    public function passError(Exception $e)
    {
        $data = $this->getFlagData();
        if (!is_array($data)) {
            $data = [];
        }
        $data['has_errors'] = true;
        $this->setFlagData($data);
        return $this;
    }
}
