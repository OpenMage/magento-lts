<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
    public function __construct($arguments = array())
    {
        /** @var Mage_Directory_Model_Region _optionsModel */
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
    public function toOptionArray($noEmpty=false, $country = null)
    {
        $options = $this->_optionsModel->getCollection()
            ->addCountryFilter($country)
            ->toOptionArray();

        if ($noEmpty) {
            unset($options[0]);
        } else {
            if ($options) {
                $options[0] = array('value' => '0', 'label' => '*');
            } else {
                $options = array(
                    array('value' => '0', 'label' => '*'),
                );
            }
        }
        return $options;
    }
}
