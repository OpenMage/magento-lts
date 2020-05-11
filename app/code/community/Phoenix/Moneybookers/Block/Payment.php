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
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category    Phoenix
 * @package     Phoenix_Moneybookers
 * @copyright   Copyright (c) 2020 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Phoenix_Moneybookers_Block_Payment extends Mage_Core_Block_Template
{
    /**
     * Return Payment logo src
     *
     * @return string
     */
    public function getMoneybookersLogoSrc()
    {
        $locale = Mage::getModel('moneybookers/acc')->getLocale();
        $logoFilename = Mage::getDesign()
            ->getFilename('images' . DS . 'moneybookers' . DS . 'banner_120_' . $locale . '.gif', array('_type' => 'skin'));

        if (file_exists($logoFilename)) {
            return $this->getSkinUrl('images/moneybookers/banner_120_'.$locale.'.gif');
        }

        return $this->getSkinUrl('images/moneybookers/banner_120_int.gif');
    }
}
