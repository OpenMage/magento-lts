<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Dataflow
 */

/**
 * DataFlow Import Model
 *
 * @package    Mage_Dataflow
 *
 * @method Mage_Dataflow_Model_Resource_Import            _getResource()
 * @method Mage_Dataflow_Model_Resource_Import_Collection getCollection()
 * @method Mage_Dataflow_Model_Resource_Import            getResource()
 * @method Mage_Dataflow_Model_Resource_Import_Collection getResourceCollection()
 */
class Mage_Dataflow_Model_Import extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('dataflow/import');
    }

    public function getSerialNumber(): int
    {
        return (int) $this->_getData('serial_number');
    }

    public function getSessionId(): ?int
    {
        $value = $this->_getData('session_id');
        return $value !== null ? (int) $value : null;
    }

    public function getStatus(): int
    {
        return (int) $this->_getData('status');
    }

    public function getValue(): string
    {
        return (string) $this->_getData('value');
    }

    public function setSerialNumber(int $value): static
    {
        return $this->setData('serial_number', $value);
    }

    public function setSessionId(?int $value): static
    {
        return $this->setData('session_id', $value);
    }

    public function setStatus(int $value): static
    {
        return $this->setData('status', $value);
    }

    public function setValue(string $value): static
    {
        return $this->setData('value', $value);
    }
}
