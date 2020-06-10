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
 * @package     Mage_Core
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
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
        $options = array();
        $packages = $this->getData('themes');
        foreach ($packages as $package) {
            $options[] = array('value'=>$package, 'label'=>$package);
        }
        array_unshift($options, array('value'=>'', 'label'=>''));

        return $options;
    }
}
