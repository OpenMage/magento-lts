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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2013 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * XmlConnect device helper abstract
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_XmlConnect_Helper_Device_Abstract extends Mage_Core_Helper_Abstract
{
    /**
     * Country renderer block
     *
     * @var string
     */
    protected $_countryRendererBlock = 'xmlconnect/adminhtml_mobile_submission_renderer_country_istore';

    /**
     * Country field renderer
     *
     * @var Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
     */
    protected $_countryRenderer;

    /**
     * Submission columns count
     *
     * @var int
     */
    protected $_countryColumnsCount = 4;

    /**
     * List of coutries that allowed in Ituens by Apple Store
     *
     * array(
     *      'country name' => 'country id at directory model'
     * )
     *
     * @var array
     */
    protected $_allowedCountries = array(
        'Argentina'     => 'AR',
        'Armenia'       => 'AM',
        'Australia'     => 'AU',
        'Austria'       => 'AT',
        'Belgium'       => 'BE',
        'Botswana'      => 'BW',
        'Brazil'        => 'BR',
        'Bulgaria'      => 'BG',
        'Canada'        => 'CA',
        'Chile'         => 'CL',
        'China'         => 'CN',
        'Colombia'      => 'CO',
        'Costa Rica'    => 'CR',
        'Croatia'       => 'HR',
        'Czech Republic' => 'CZ',
        'Denmark'       => 'DK',
        'Dominican Republic' => 'DO',
        'Ecuador'       => 'EC',
        'Egypt'         => 'EG',
        'El Salvador'   => 'SV',
        'Estonia'       => 'EE',
        'Finland'       => 'FI',
        'France'        => 'FR',
        'Germany'       => 'DE',
        'Greece'        => 'GR',
        'Guatemala'     => 'GT',
        'Honduras'      => 'HN',
        'Hong Kong SAR China' => 'HK',
        'Hungary'       => 'HU',
        'India'         => 'IN',
        'Indonesia'     => 'ID',
        'Ireland'       => 'IE',
        'Israel'        => 'IL',
        'Italy'         => 'IT',
        'Jamaica'       => 'JM',
        'Japan'         => 'JP',
        'Jordan'        => 'JO',
        'Kazakstan'     => 'KZ',
        'Kenya'         => 'KE',
        'South Korea'   => 'KR',
        'Kuwait'        => 'KW',
        'Latvia'        => 'LV',
        'Lebanon'       => 'LB',
        'Lithuania'     => 'LT',
        'Luxembourg'    => 'LU',
        'Macau SAR China' => 'MO',
        'Macedonia'     => 'MK',
        'Madagascar'    => 'MG',
        'Malaysia'      => 'MY',
        'Mali'          => 'ML',
        'Malta'         => 'MT',
        'Mauritius'     => 'MU',
        'Mexico'        => 'MX',
        'Moldova'       => 'MD',
        'Netherlands'   => 'NL',
        'New Zealand'   => 'NZ',
        'Nicaragua'     => 'NI',
        'Niger'         => 'NE',
        'Norway'        => 'NO',
        'Pakistan'      => 'PK',
        'Panama'        => 'PA',
        'Paraguay'      => 'PY',
        'Peru'          => 'PE',
        'Philippines'   => 'PH',
        'Poland'        => 'PL',
        'Portugal'      => 'PT',
        'Qatar'         => 'QA',
        'Romania'       => 'RO',
        'Russia'        => 'RU',
        'Saudi Arabia'  => 'SA',
        'Senegal'       => 'SN',
        'Singapore'     => 'SG',
        'Slovakia'      => 'SK',
        'Slovenia'      => 'SI',
        'South Africa'  => 'ZA',
        'Spain'         => 'ES',
        'Sri Lanka'     => 'LK',
        'Sweden'        => 'SE',
        'Switzerland'   => 'CH',
        'Taiwan'        => 'TW',
        'Thailand'      => 'TH',
        'Tunisia'       => 'TN',
        'Turkey'        => 'TR',
        'Uganda'        => 'UG',
        'United Arab Emirates' => 'AE',
        'United Kingdom' => 'GB',
        'United States' => 'US',
        'Uruguay'       => 'UY',
        'Venezuela'     => 'VE',
        'Vietnam'       => 'VN',
    );

    /**
     * Get submit images that are required for application submit
     *
     * @return array
     */
    public function getSubmitImages()
    {
        return $this->_imageIds;
    }

    /**
     * Default images list
     *
     * @return array
     */
    abstract function getImagesTypesList();

    /**
     * Get default application tabs
     *
     * @return array
     */
    abstract function getDefaultDesignTabs();

    /**
     * Default application configuration: font and color
     *
     * @return array
     */
    abstract function getDefaultConfiguration();

    /**
     * List of allowed fonts for application
     *
     * @return array
     */
    abstract function getFontList();

    /**
     * List of allowed font sizes for application
     *
     * @return array
     */
    public function getFontSizes()
    {
        $result = array();
        for ($i = 6; $i < 32; $i++) {
            $result[] = array(
                'value' => $i,
                'label' => $i . ' pt',
            );
        }
        return $result;
    }

    /**
     * Get list of countries that allowed by Magento Inc. for iOS
     *
     *
     * @return array
     */
    public function getAllowedCountriesArray()
    {
        return $this->_allowedCountries;
    }

    /**
     * Validate submit application data
     *
     * @param array $params
     * @return array
     */
    abstract function validateSubmit($params);

    /**
     * Get renderer for submission country
     *
     * @return Mage_Adminhtml_Block_Widget_Form_Renderer_Fieldset_Element
     */
    public function getCountryRenderer()
    {
        if (empty($this->_countryRenderer)) {
            $this->_countryRenderer = Mage::app()->getLayout()->createBlock($this->_countryRendererBlock);
        }
        return $this->_countryRenderer;
    }

    /**
     * Get label for submission country
     *
     * @return string
     */
    public function getCountryLabel()
    {
        return Mage::helper('xmlconnect')->__('App Stores');
    }

    /**
     * Get count of columns for submission country
     *
     * @return int
     */
    public function getCountryColumns()
    {
        return $this->_countryColumnsCount;
    }

    /**
     * Get placement of Country Names for submission country
     *
     * @return bool
     */
    public function isCountryNamePlaceLeft()
    {
        return true;
    }

    /**
     * Get class name for submission country
     *
     * @return string
     */
    public function getCountryClass()
    {
        return 'istore stripy';
    }

    /**
     * Check the notifications are allowed for current type of application
     *
     * @return bool
     */
    public function isNotificationsAllowed()
    {
        return true;
    }

    /**
     * Get image count for image type
     *
     * @param string $imageType
     * @return int
     */
    public function getImageCount($imageType)
    {
        $imagesTypesList = $this->getImagesTypesList();
        if (!array_key_exists($imageType, $imagesTypesList)) {
            Mage::throwException(Mage::helper('xmlconnect')->__('Image type doesn\'t recognized: "%s".', $imageType));
        }
        return !empty($imagesTypesList[$imageType]['count']) ? $imagesTypesList[$imageType]['count'] : 0;
    }
}
