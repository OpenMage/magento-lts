<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Core
 */

/**
 * Design package collection
 *
 * @package    Mage_Core
 */
class Mage_Core_Model_Resource_Design_Package_Collection extends Varien_Object
{
    /**
     * Load design package collection
     *
     * @return $this
     */
    public function load()
    {
        $packages = $this->getData('packages');
        if (is_null($packages)) {
            $packages = Mage::getModel('core/design_package')->getPackageList();
            $this->setData('packages', $packages);
        }

        return $this;
    }

    /**
     * Convert to option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $packages = $this->getData('packages');
        foreach ($packages as $package) {
            $options[] = ['value' => $package, 'label' => $package];
        }

        array_unshift($options, ['value' => '', 'label' => '']);

        return $options;
    }
}
