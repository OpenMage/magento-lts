<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogIndex
 */

/**
 * @package    Mage_CatalogIndex
 */
class Mage_CatalogIndex_Model_Catalog_Index_Flag extends Mage_Core_Model_Flag
{
    protected $_flagCode = 'catalogindex';

    public const STATE_QUEUED = 1;
    public const STATE_RUNNING = 2;

    /**
     * @return Mage_Core_Model_Flag
     */
    protected function _beforeSave()
    {
        switch ($this->getState()) {
            case self::STATE_QUEUED:
                $this->setFlagData($this->getQueueInfo());
                break;

            case self::STATE_RUNNING:
                $this->setFlagData(getmypid());
                break;

            default:
                break;
        }

        return parent::_beforeSave();
    }
}
