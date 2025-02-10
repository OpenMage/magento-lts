<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Model_System_Config_Source_Frequency
{
    /**
     * @return array[]
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => Mage::helper('sitemap')->__('Do not show in sitemap')],
            ['value' => 'always', 'label' => Mage::helper('sitemap')->__('Always')],
            ['value' => 'hourly', 'label' => Mage::helper('sitemap')->__('Hourly')],
            ['value' => 'daily', 'label' => Mage::helper('sitemap')->__('Daily')],
            ['value' => 'weekly', 'label' => Mage::helper('sitemap')->__('Weekly')],
            ['value' => 'monthly', 'label' => Mage::helper('sitemap')->__('Monthly')],
            ['value' => 'yearly', 'label' => Mage::helper('sitemap')->__('Yearly')],
            ['value' => 'never', 'label' => Mage::helper('sitemap')->__('Never')],
        ];
    }
}
