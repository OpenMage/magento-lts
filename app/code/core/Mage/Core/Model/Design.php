<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_Design            _getResource()
 * @method Mage_Core_Model_Resource_Design_Collection getCollection()
 * @method string                                     getPackage()
 * @method Mage_Core_Model_Resource_Design            getResource()
 * @method Mage_Core_Model_Resource_Design_Collection getResourceCollection()
 * @method string                                     getTheme()
 */
class Mage_Core_Model_Design extends Mage_Core_Model_Abstract
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('core/design');
    }

    /**
     * @return $this
     */
    public function validate()
    {
        $this->getResource()->validate($this);
        return $this;
    }

    /**
     * @param  int         $storeId
     * @param  null|string $date
     * @return $this
     */
    public function loadChange($storeId, $date = null)
    {
        $result = $this->getResource()
            ->loadChange($storeId, $date);

        if (!empty($result)) {
            if (!empty($result['design'])) {
                $tmp = explode('/', $result['design']);
                $result['package'] = $tmp[0];
                $result['theme'] = $tmp[1];
            }

            $this->setData($result);
        }

        return $this;
    }

    public function getDateFrom(): string
    {
        return (string) $this->_getData('date_from');
    }

    public function setDateFrom(string $value): static
    {
        return $this->setData('date_from', $value);
    }

    public function getDateTo(): string
    {
        return (string) $this->_getData('date_to');
    }

    public function setDateTo(string $value): static
    {
        return $this->setData('date_to', $value);
    }

    public function getDesign(): string
    {
        return (string) $this->_getData('design');
    }

    public function setDesign(string $value): static
    {
        return $this->setData('design', $value);
    }

    public function getStoreId(): int
    {
        return (int) $this->_getData('store_id');
    }

    public function setStoreId(int $value): static
    {
        return $this->setData('store_id', $value);
    }
}
