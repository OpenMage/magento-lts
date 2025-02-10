<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Core
 * @method array getData(string $value)
 * @method $this setData(string $value, array $value)
 */
class Mage_Core_Model_Mysql4_Design_Theme_Collection extends Varien_Directory_Collection
{
    /**
     * @return $this
     */
    public function load()
    {
        $packages = $this->getData('themes');
        if (is_null($packages)) {
            $packages = Mage::getModel('core/design_package')->getThemeList();
            $this->setData('themes', $packages);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $packages = $this->getData('themes');
        foreach ($packages as $package) {
            $options[] = ['value' => $package, 'label' => $package];
        }
        array_unshift($options, ['value' => '', 'label' => '']);

        return $options;
    }
}
