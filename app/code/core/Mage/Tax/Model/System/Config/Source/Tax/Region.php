<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Tax
 */
class Mage_Tax_Model_System_Config_Source_Tax_Region
{
    /**
     * @var Mage_Directory_Model_Region|null
     */
    protected $_optionsModel;

    /**
     * @param array $arguments
     */
    public function __construct($arguments = [])
    {
        $this->_optionsModel = !empty($arguments['region_model'])
            ? $arguments['region_model'] : Mage::getModel('directory/region');
    }

    /**
     * Return list of country's regions as array
     *
     * @param bool $noEmpty
     * @param null|string $country
     * @return array
     */
    public function toOptionArray($noEmpty = false, $country = null)
    {
        $options = $this->_optionsModel->getCollection()
            ->addCountryFilter($country)
            ->toOptionArray();

        if ($noEmpty) {
            unset($options[0]);
        } else {
            if ($options) {
                $options[0] = ['value' => '0', 'label' => '*'];
            } else {
                $options = [
                    ['value' => '0', 'label' => '*'],
                ];
            }
        }
        return $options;
    }
}
