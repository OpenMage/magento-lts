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
 * @package     Mage_Oscommerce
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * osCommerce edit form
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oscommerce_Block_Adminhtml_Import_Edit_Tab_Run extends Mage_Adminhtml_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('oscommerce/convert/run.phtml');
    }

    /**
     * Prepares layout of block
     *
     */
    protected function _prepareLayout()
    {
        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('oscommerce')->__('Start Runing!'),
                    'class'   => 'run',
                    'id'      => 'run_import'
                ))

        );

        $this->setChild('check_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'   => Mage::helper('oscommerce')->__('Check requirements!'),
                    'class'   => 'run',
                    'id'      => 'check_import'
                ))

        );

    }

    public function getImportId()
    {
        return Mage::registry('oscommerce_adminhtml_import')->getId();
    }

    /**
     * Retrieve run url
     *
     * @return string
     */
    public function getSubmitUrl()
    {
        return $this->getUrl('*/*/run/', array('id'=>$this->getOscId()));
    }

    /**
     * Retrive run button html
     *
     * @return string
     */
    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getCheckButtonHtml()
    {
        return $this->getChildHtml('check_button');
    }

    public function getWebsiteOptionHtml()
    {

        $html  = '<select id="website" name="website">';
        $html .= '  <option value="">'.Mage::helper('oscommerce')->__('Select a website'). '</option>';
        $websites = Mage::app()->getWebsites();
        $websiteData = array();
        if ($websites) foreach($websites as $website) {
            $html .= '<option value='. $website->getId() . '>' . $website->getName() . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    public function getTimezoneOptionHtml()
    {
        $html  = '<select id="timezone" name="timezone">';
        $html .= '  <option value="">'.Mage::helper('oscommerce')->__('Select a timezone'). '</option>';
        $options = Mage::getModel('core/locale')->getOptionTimezones();
        if ($options) foreach($options as $option) {
            $html .= '<option value='. $option['value'] . '>' . $option['label'] . '</option>';
        }
        $html .= '</select>';
        return $html;

    }

    /**
     * Get list available for mysql connection charsets
     *
     * @return array
     */
    public function getConnectionCharsets()
    {
        $charsetList = array();
        $fileName = Mage::getModuleDir('etc','Mage_Oscommerce').DS.'charsets.xml';
        if (is_readable($fileName)) {
            $xml = new Varien_Simplexml_Config();
            $xml->loadFile($fileName);
            $charsets = $xml->getNode('charset');
            foreach($charsets as $charset) {
                $attributes = $charset->attributes();
                $code = (string) $attributes['name'];
                $charsetList[$code] = (string)$charset->family;
            }
        }
        return $charsetList;
    }

    /**
     * Get list available for iconv function charsets
     *
     * @return array
     */
    public function getDataCharsets()
    {
        $charsetList = array(
            'BIG-5'         => Mage::helper('oscommerce')->__('Traditional Chinese'),
            'ISO-8859-2'    => Mage::helper('oscommerce')->__('Central European'),
            'CP850'         => Mage::helper('oscommerce')->__('Western'),
            'ISO-8859-1'    => Mage::helper('oscommerce')->__('Western'),
            'HP-ROMAN8'     => Mage::helper('oscommerce')->__('Western'),
            'KOI8-R'        => Mage::helper('oscommerce')->__('Cyrillic'),
            'ASCII'         => Mage::helper('oscommerce')->__('Western'),
            'EUC-JP'        => Mage::helper('oscommerce')->__('Japanese'),
            'SHIFT-JIS'     => Mage::helper('oscommerce')->__('Japanese'),
            'windows-1251'  => Mage::helper('oscommerce')->__('Cyrillic'),
            'ISO-8859-8'    => Mage::helper('oscommerce')->__('Hebrew'),
            'TIS-620'       => Mage::helper('oscommerce')->__('Thai'),
            'EUC-KR'        => Mage::helper('oscommerce')->__('Korean'),
            'ISO-8859-13'   => Mage::helper('oscommerce')->__('Baltic'),
            'KOI8-U'        => Mage::helper('oscommerce')->__('Cyrillic'),
            'CHINESE'       => Mage::helper('oscommerce')->__('Simplified Chinese'),
            'ISO-8859-7'    => Mage::helper('oscommerce')->__('Greek'),
            'WINDOWS-1250'  => Mage::helper('oscommerce')->__('Central European'),
            'CP936'         => Mage::helper('oscommerce')->__('East Asian'),
            'WINDOWS-1257'  => Mage::helper('oscommerce')->__('Baltic'),
            'ISO-8859-9'    => Mage::helper('oscommerce')->__('South Asian'),
            'ARMSCII-8'     => Mage::helper('oscommerce')->__('South Asian'),
            'UTF-8'         => Mage::helper('oscommerce')->__('Unicode'),
            'UCS-2'         => Mage::helper('oscommerce')->__('Unicode'),
            'CP866'         => Mage::helper('oscommerce')->__('Cyrillic'),
            'MACCENTRALEUROPE' => Mage::helper('oscommerce')->__('Central European'),
            'MAC'           => Mage::helper('oscommerce')->__('Western'),
            'CP852'         => Mage::helper('oscommerce')->__('Central European'),
            'CP1256'        => Mage::helper('oscommerce')->__('Arabic'),
            'CP932'         => Mage::helper('oscommerce')->__('Japanese'),
        );
        return $charsetList;
    }

    public function drowOptions($options = array())
    {
        asort($options);
        $html = '';
        foreach($options as $code => $name) {
            $html.= '<option value='. $code . '>' . $name . ' ('. $code .')</option>';
        }

        return $html;
    }

    public function getDataCharsetOptionHtml()
    {

        $html  = '<select id="data_charset" name="data_charset">';
        $html .= '  <option value="">'.Mage::helper('oscommerce')->__('Select a data charset'). '</option>';
        $html .= $this->drowOptions($this->getDataCharsets());
        $html .= '</select>';
        return $html;
    }

    public function getConnectionCharsetOptionHtml()
    {
        $html  = '<select id="connection_charset" name="connection_charset">';
        $html .= '  <option value="">'.Mage::helper('oscommerce')->__('Select a connection charset'). '</option>';
        $html .= $this->drowOptions($this->getConnectionCharsets());
        $html .= '</select>';
        return $html;
    }

    /**
     * Deprecated
     *
     * @return string
     */
    public function getCharsetOption()
    {
        $options = '';
        $fileName = Mage::getModuleDir('etc','Mage_Oscommerce').DS.'charsets.xml';
        if (is_readable($fileName)) {
            $xml = new Varien_Simplexml_Config();
            $xml->loadFile($fileName);
            $charsets = $xml->getNode('charset');
            foreach($charsets as $charset) {
                $attributes = $charset->attributes();
                $options .= '<option value='. $attributes['name'] . '>' . $charset->family . ' ('. $attributes['name'] .')</option>';
            }
        }
        return $options;
    }
}
