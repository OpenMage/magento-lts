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
 * @package     Mage_Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Adminhtml footer block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Page_Footer extends Mage_Adminhtml_Block_Template
{

    protected function _construct()
    {
        $this->setTemplate('page/footer.phtml');
        $this->setShowProfiler(true);
    }

    public function getChangeLocaleUrl()
    {
        return $this->getUrl('adminhtml/index/changeLocale');
    }

    public function getUrlForReferer()
    {
        return $this->getUrlEncoded('*/*/*',array('_current'=>true));
    }

    public function getRefererParamName()
    {
        return Mage_Core_Controller_Varien_Action::PARAM_NAME_URL_ENCODED;
    }

    public function getLanguageSelect()
    {
        $html = $this->getLayout()->createBlock('adminhtml/html_select')
            ->setName('locale')
            ->setId('interface_locale')
            ->setTitle(Mage::helper('page')->__('Interface Language'))
            ->setExtraParams('style="width:200px"')
            ->setValue(Mage::app()->getLocale()->getLocaleCode())
            ->setOptions(Mage::app()->getLocale()->getTranslatedOptionLocales())
            ->getHtml();
        return $html;
    }

}
