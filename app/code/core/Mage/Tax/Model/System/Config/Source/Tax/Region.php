<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tax
 */

/**
 * @package    Mage_Tax
 */
class Mage_Tax_Model_System_Config_Source_Tax_Region
{
    /**
     * @var null|Mage_Directory_Model_Region
     */
    protected $_optionsModel;

    /**
     * @param array $arguments
     */
    public function __construct($arguments = [])
    {
        $this->_optionsModel = empty($arguments['region_model'])
            ? Mage::getModel('directory/region') : $arguments['region_model'];
    }

    /**
     * Return list of country's regions as array
     *
     * @param  bool        $noEmpty
     * @param  null|string $country
     * @return array
     */
    public function toOptionArray($noEmpty = false, $country = null)
    {
        $options = $this->_optionsModel->getCollection()
            ->addCountryFilter($country)
            ->toOptionArray();

        if ($noEmpty) {
            unset($options[0]);
        } elseif ($options) {
            $options[0] = ['value' => '0', 'label' => '*'];
        } else {
            $options = [
                ['value' => '0', 'label' => '*'],
            ];
        }

        return $options;
    }
}
