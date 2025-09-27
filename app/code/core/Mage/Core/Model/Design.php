<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * @package    Mage_Core
 *
 * @method Mage_Core_Model_Resource_Design _getResource()
 * @method Mage_Core_Model_Resource_Design getResource()
 * @method int getStoreId()
 * @method $this setStoreId(int $value)
 * @method string getDesign()
 * @method $this setDesign(string $value)
 * @method string getDateFrom()
 * @method $this setDateFrom(string $value)
 * @method string getDateTo()
 * @method $this setDateTo(string $value)
 * @method string getPackage()
 * @method string getTheme()
 */
class Mage_Core_Model_Design extends Mage_Core_Model_Abstract
{
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
     * @param int $storeId
     * @param string|null $date
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
}
