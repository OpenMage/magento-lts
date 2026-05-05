<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * DataFlow Session Model
 *
 * @package    Mage_Dataflow
 *
 * @method Mage_Dataflow_Model_Resource_Session _getResource()
 * @method Mage_Dataflow_Model_Resource_Session getResource()
 */
class Mage_Dataflow_Model_Session extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('dataflow/session');
    }

    public function getComment(): string
    {
        return (string) $this->_getData('comment');
    }

    public function getCreatedDate(): string
    {
        return (string) $this->_getData('created_date');
    }

    public function getDirection(): string
    {
        return (string) $this->_getData('direction');
    }

    public function getFile(): string
    {
        return (string) $this->_getData('file');
    }

    public function getType(): string
    {
        return (string) $this->_getData('type');
    }

    public function getUserId(): int
    {
        return (int) $this->_getData('user_id');
    }

    public function setComment(string $value): static
    {
        return $this->setData('comment', $value);
    }

    public function setCreatedDate(string $value): static
    {
        return $this->setData('created_date', $value);
    }

    public function setDirection(string $value): static
    {
        return $this->setData('direction', $value);
    }

    public function setFile(string $value): static
    {
        return $this->setData('file', $value);
    }

    public function setType(string $value): static
    {
        return $this->setData('type', $value);
    }

    public function setUserId(int $value): static
    {
        return $this->setData('user_id', $value);
    }
}
