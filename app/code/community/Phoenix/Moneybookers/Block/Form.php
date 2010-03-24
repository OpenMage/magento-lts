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
 * @copyright   Copyright (c) 2009 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Phoenix_Moneybookers_Block_Form extends Mage_Payment_Block_Form
{
    /**
     * Constructor. Set template.
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('moneybookers/form.phtml');
    }

    /**
     * Return payment logo image src
     *
     * @param string $payment Payment Code
     * @return string|bool
     */
    public function getPaymentImageSrc($payment)
    {
        $imageFilename = Mage::getDesign()
            ->getFilename('images' . DS . 'moneybookers' . DS . $payment, array('_type' => 'skin'));

        if (file_exists($imageFilename . '.png')) {
            return $this->getSkinUrl('images/moneybookers/' . $payment . '.png');
        } else if (file_exists($imageFilename . '.gif')) {
            return $this->getSkinUrl('images/moneybookers/' . $payment . '.gif');
        }

        return false;
    }
}
