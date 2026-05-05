<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Log
 */

/**
 * Log Model
 *
 * @package    Mage_Log
 *
 * @method Mage_Log_Model_Resource_Log _getResource()
 * @method Mage_Log_Model_Resource_Log getResource()
 */
class Mage_Log_Model_Log extends Mage_Core_Model_Abstract
{
    public const XML_LOG_CLEAN_DAYS    = 'system/log/clean_after_day';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('log/log');
    }

    public function getFirstVisitAt(): string
    {
        return (string) $this->_getData('first_visit_at');
    }

    public function getLastUrlId(): int
    {
        return (int) $this->_getData('last_url_id');
    }

    public function getLastVisitAt(): string
    {
        return (string) $this->_getData('last_visit_at');
    }

    public function getSessionId(): ?string
    {
        return $this->_getData('session_id') !== null ? (string) $this->_getData('session_id') : null;
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function setFirstVisitAt(string $value): static
    {
        return $this->setData('first_visit_at', $value);
    }

    public function setLastUrlId(int $value): static
    {
        return $this->setData('last_url_id', $value);
    }

    public function setLastVisitAt(string $value): static
    {
        return $this->setData('last_visit_at', $value);
    }

    public function setSessionId(?string $value): static
    {
        return $this->setData('session_id', $value);
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }

    /**
     * @return int
     */
    public function getLogCleanTime()
    {
        return Mage::getStoreConfigAsInt(self::XML_LOG_CLEAN_DAYS) * 60 * 60 * 24;
    }

    /**
     * Clean Logs
     *
     * @return $this
     */
    public function clean()
    {
        $this->getResource()->clean($this);
        return $this;
    }
}
