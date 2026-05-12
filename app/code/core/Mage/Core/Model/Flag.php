<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Core Flag model
 *
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_Flag _getResource()
 * @method Mage_Core_Model_Resource_Flag getResource()
 * @method bool                          hasFlagData()
 */
class Mage_Core_Model_Flag extends Mage_Core_Model_Abstract
{
    /**
     * Flag code
     *
     * @var null|string
     */
    protected $_flagCode = null;

    /**
     * Init resource model
     * Set flag_code if it is specified in arguments
     */
    protected function _construct()
    {
        if ($this->hasData('flag_code')) {
            $this->_flagCode = $this->getDataByKey('flag_code');
        }

        $this->_init('core/flag');
    }

    public function getFlagCode(): string
    {
        return (string) $this->_getData('flag_code');
    }

    public function getLastUpdate(): string
    {
        return (string) $this->_getData('last_update');
    }

    public function getState(): int
    {
        return (int) $this->_getData('state');
    }

    public function setFlagCode(string $value): static
    {
        return $this->setData('flag_code', $value);
    }

    public function setLastUpdate(string $value): static
    {
        return $this->setData('last_update', $value);
    }

    public function setState(int $value): static
    {
        return $this->setData('state', $value);
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected function _beforeSave()
    {
        if (is_null($this->_flagCode)) {
            Mage::throwException(Mage::helper('core')->__('Please define flag code.'));
        }

        $this->setFlagCode($this->_flagCode);
        $this->setLastUpdate(date(Varien_Db_Adapter_Pdo_Mysql::TIMESTAMP_FORMAT));

        return parent::_beforeSave();
    }

    /**
     * Retrieve flag data
     *
     * @return mixed
     */
    public function getFlagData()
    {
        if ($this->hasFlagData()) {
            return unserialize($this->getDataByKey('flag_data'), ['allowed_classes' => false]);
        }

        return null;
    }

    /**
     * Set flag data
     *
     * @param  mixed $value
     * @return $this
     */
    public function setFlagData($value)
    {
        return $this->setData('flag_data', serialize($value));
    }

    /**
     * load self (load by flag code)
     *
     * @return $this
     */
    public function loadSelf()
    {
        if (is_null($this->_flagCode)) {
            Mage::throwException(Mage::helper('core')->__('Please define flag code.'));
        }

        return $this->load($this->_flagCode, 'flag_code');
    }
}
