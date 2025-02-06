<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Core
 */

/**
 * Synchronize process status flag class
 *
 * @category   Mage
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
