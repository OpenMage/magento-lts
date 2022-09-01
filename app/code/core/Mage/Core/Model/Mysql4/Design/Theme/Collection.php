<?php
/**
 * OpenMage
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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 *
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
            $options[] = ['value'=>$package, 'label'=>$package];
        }
        array_unshift($options, ['value'=>'', 'label'=>'']);

        return $options;
    }
}
