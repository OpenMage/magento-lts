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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * JavaScript helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Helper_Js extends Mage_Core_Helper_Abstract
{
    /**
     * Array of senteces of JS translations
     *
     * @var array
     */
    protected $_translateData = array();

    public function __construct()
    {
        $this->_translateData = array(
            'Please select an option.' => $this->__('Please select an option.'),
            'This is a required field.' => $this->__('This is a required field.'),
            'Please enter a valid number in this field.' => $this->__('Please enter a valid number in this field.'),
            'Please use numbers only in this field. please avoid spaces or other characters such as dots or commas.' =>
                $this->__('Please use numbers only in this field. please avoid spaces or other characters such as dots or commas.'),
            'Please use letters only (a-z) in this field.' => $this->__('Please use letters only (a-z) in this field.'),
            'Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter.' =>
                $this->__('Please use only letters (a-z), numbers (0-9) or underscore(_) in this field, first character should be a letter.'),
            'Please use only letters (a-z) or numbers (0-9) only in this field. No spaces or other characters are allowed.' =>
                $this->__('Please use only letters (a-z) or numbers (0-9) only in this field. No spaces or other characters are allowed.'),
            'Please use only letters (a-z) or numbers (0-9) or spaces and # only in this field.' =>
                $this->__('Please use only letters (a-z) or numbers (0-9) or spaces and # only in this field.'),
            'Please enter a valid phone number. For example (123) 456-7890 or 123-456-7890.' =>
                $this->__('Please enter a valid phone number. For example (123) 456-7890 or 123-456-7890.'),
            'Please enter a valid date.' => $this->__('Please enter a valid date.'),
            'Please enter a valid email address. For example johndoe@domain.com.' =>
                $this->__('Please enter a valid email address. For example johndoe@domain.com.'),
            'Please enter 6 or more characters.' => $this->__('Please enter 6 or more characters.'),
            'Please make sure your passwords match.' => $this->__('Please make sure your passwords match.'),
            'Please enter a valid URL. http:// is required' => $this->__('Please enter a valid URL. http:// is required'),
            'Please enter a valid URL. For example http://www.example.com or www.example.com' =>
                $this->__('Please enter a valid URL. For example http://www.example.com or www.example.com'),
            'Please enter a valid social security number. For example 123-45-6789.' =>
                $this->__('Please enter a valid social security number. For example 123-45-6789.'),
            'Please enter a valid zip code. For example 90602 or 90602-1234.' =>
                $this->__('Please enter a valid zip code. For example 90602 or 90602-1234.'),
            'Please enter a valid zip code.' => $this->__('Please enter a valid zip code.'),
            'Please use this date format: dd/mm/yyyy. For example 17/03/2006 for the 17th of March, 2006.' =>
                $this->__('Please use this date format: dd/mm/yyyy. For example 17/03/2006 for the 17th of March, 2006.'),
            'Please enter a valid $ amount. For example $100.00.' =>
                $this->__('Please enter a valid $ amount. For example $100.00.'),
            'Please select one of the above options.' => $this->__('Please select one of the above options.'),
            'Please select one of the options.' => $this->__('Please select one of the options.'),
            'Please enter a valid number in this field.' => $this->__('Please enter a valid number in this field.'),
            'Please select State/Province.' => $this->__('Please select State/Province.'),
            'Please enter valid password.' => $this->__('Please enter valid password.'),
            'Please enter 6 or more characters. Leading or trailing spaces will be ignored.' =>
                $this->__('Please enter 6 or more characters. Leading or trailing spaces will be ignored.'),
            'Please use letters only (a-z or A-Z) in this field.' => $this->__('Please use letters only (a-z or A-Z) in this field.'),
            'Please enter a number greater than 0 in this field.' =>
                $this->__('Please enter a number greater than 0 in this field.'),
            'Please enter a valid credit card number.' => $this->__('Please enter a valid credit card number.'),
            'Please wait, loading...' => $this->__('Please wait, loading...'),
            'Please choose to register or to checkout as a guest' => $this->__('Please choose to register or to checkout as a guest'),
            'Error: Passwords do not match' => $this->__('Error: Passwords do not match'),
            'Your order can not be completed at this time as there is no shipping methods available for it. Please make necessary changes in your shipping address.' =>
                $this->__('Your order can not be completed at this time as there is no shipping methods available for it. Please make neccessary changes in your shipping address.'),
            'Please specify shipping method.' => $this->__('Please specify shipping method.'),
            'Your order can not be completed at this time as there is no payment methods available for it.' =>
                $this->__('Your order can not be completed at this time as there is no payment methods available for it.'),
            'Please specify payment method.' => $this->__('Please specify payment method.'),

//Mage_Rule

            'Your session has been expired, you will be relogged in now.' => $this->__('Your session has been expired, you will be relogged in now.'),
            'Incorrect credit card expiration date' => $this->__('Incorrect credit card expiration date'),
        );
    }

    /**
     * Retrieve JSON of JS sentences translation
     *
     * @return string
     */
    public function getTranslateJson()
    {
        return Zend_Json::encode($this->_getTranslateData());
    }

    /**
     * Retrieve JS translator initialization javascript
     *
     * @return string
     */
    public function getTranslatorScript()
    {
        $script = 'var Translator = new Translate('.$this->getTranslateJson().');';
        return $this->getScript($script);
    }

    /**
     * Retrieve framed javascript
     *
     * @param   string $script
     * @return  script
     */
    public function getScript($script)
    {
        return '<script type="text/javascript">'.$script.'</script>';
    }

    /**
     * Retrieve javascript include code
     *
     * @param   string $file
     * @return  string
     */
    public function includeScript($file)
    {
        return '<script type="text/javascript" src="'.$this->getJsUrl($file).'"></script>'."\n";
    }

    /**
     * Retrieve
     *
     * @param   string $file
     * @return  string
     */
    public function includeSkinScript($file)
    {
        return '<script type="text/javascript" src="'.$this->getJsSkinUrl($file).'"></script>';
    }

    /**
     * Retrieve JS file url
     *
     * @param   string $file
     * @return  string
     */
    public function getJsUrl($file)
    {
        return Mage::getBaseUrl('js').$file;
    }

    /**
     * Retrieve skin JS file url
     *
     * @param   string $file
     * @return  string
     */
    public function getJsSkinUrl($file)
    {
        return Mage::getDesign()->getSkinUrl($file, array());
    }

    /**
     * Retrieve JS translation array
     *
     * @return array
     */
    protected function _getTranslateData()
    {
        return $this->_translateData;
    }

}
