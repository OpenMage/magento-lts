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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Paypal Data helper
 */
class Mage_Paypal_Helper_Data extends Mage_Core_Helper_Abstract
{
    //@ phpdoc
    protected $_logoList = array(
        'de_DE' => 'https://www.paypal.com/de_DE/DE/i/logo/lockbox_150x65.gif',
        'en_GB' => 'https://www.paypal.com/en_GB/i/bnr/vertical_solution_PP.gif',
        'en_US' => 'https://www.paypal.com/en_US/i/bnr/horizontal_solution_PP.gif',
        'fr_FR' => 'https://www.paypal.com/fr_FR/FR/i/bnr/bnr_wePrefer_150x60.gif',
        'it_IT' => 'https://www.paypal.com/it_IT/IT/i/bnr/bnr_horizontal_solution_PP_178wx80h.gif',
        'es_ES' => 'https://www.paypal.com/en_US/ES/i/bnr/bnr_horizontal_solution_PP_178wx80h.gif',
    );

    /**
     * Return logo url by given locale code
     *
     * @return string
     */
    public function getLogo()
    {
        $locale = Mage::app()->getLocale()->getLocaleCode();
        if (!empty($this->_logoList[$locale])) {
            return $this->_logoList[$locale];
        } else {
            return $this->_logoList['en_US'];
        }
    }

    /**
     * Return user language
     *
     * @return string
     */
    public function getLanguage()
    {
        return substr(Mage::app()->getLocale()->getLocaleCode(), 0, 2);
    }
}
