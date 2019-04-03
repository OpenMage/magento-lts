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
 * @package     Mage_XmlConnect
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Device submission container block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Submission_Tab_Container_Submission
    extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

     /**
     * Constructor
     * Setting view parameters
     */
    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * Adding preview for images if application was submitted(so we have saved images)
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        $block = $this->getLayout()->createBlock('adminhtml/template')
            ->setTemplate('xmlconnect/submission/app_icons_preview.phtml')
            ->setImages(Mage::helper('xmlconnect')->getApplication()->getImages());
        $this->setChild('images', $block);
        parent::_prepareLayout();
    }

    /**
     * Add image uploader to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldName
     * @param string $title
     * @param string $note
     * @param string $default
     * @param boolean $required
     */
    public function addImage($fieldset, $fieldName, $title, $note = '', $default = '', $required = false)
    {
        $fieldset->addField($fieldName, 'image', array(
            'name'      => $fieldName,
            'label'     => $title,
            'note'      => !empty($note) ? $note : null,
            'default_value' => $default,
            'required'  => $required,
        ));
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $deviceType = Mage::helper('xmlconnect')->getDeviceType();
        $form = new Varien_Data_Form();
        $this->setForm($form);
        /** @var $app Mage_XmlConnect_Model_Application */
        $app = Mage::helper('xmlconnect')->getApplication();
        $form->setAction($this->getUrl('*/mobile/submission'));
        $isResubmit = $app->getIsResubmitAction();
        $formData = $app->getFormData();

        $url = Mage::getStoreConfig('xmlconnect/mobile_application/activation_key_url');
        $afterElementHtml = $this->__('In order to submit your app, you need to first purchase a <a href="%s" target="_blank">%s</a> from MagentoCommerce', $url, $this->__('Activation Key'));
        $fieldset = $form->addFieldset('submit_keys', array('legend' => $this->__('Key')));
        $field = $fieldset->addField('conf[submit_text][key]', 'text', array(
            'name'      => 'conf[submit_text][key]',
            'label'     => $this->__('Activation Key'),
            'value'     => isset($formData['conf[submit_text][key]']) ? $formData['conf[submit_text][key]'] : null,
            'after_element_html' => $afterElementHtml,
        ));
        if (!$isResubmit) {
            $field->setRequired(true);
        } else {
            $field->setDisabled('disabled');
            $fieldset->addField('conf[submit_text][key]_hidden', 'hidden', array(
                'name'      => 'conf[submit_text][key]',
                'value'     => isset($formData['conf[submit_text][key]']) ? $formData['conf[submit_text][key]'] : null,
            ));
        }

        if ($isResubmit) {
            $url = Mage::getStoreConfig('xmlconnect/mobile_application/resubmission_key_url');
            $rsText = $this->__('Resubmission Key');
            $afterElementHtml = $this->__('In order to resubmit your app, you need to first purchase a <a href="%s" target="_blank">%s</a> from MagentoCommerce', $url, $rsText);

            if (isset($formData['conf[submit_text][resubmission_activation_key]'])) {
                $rsKeyVal = $formData['conf[submit_text][resubmission_activation_key]'];
            } else {
                $rsKeyVal = null;
            }

            $fieldset->addField('conf[submit_text][resubmission_activation_key]', 'text', array(
                'name'     => 'conf[submit_text][resubmission_activation_key]',
                'label'    => $this->__('Resubmission Key'),
                'value'    => $rsKeyVal,
                'required' => true,
                'after_element_html' => $afterElementHtml,
            ));
        }

        $fieldset = $form->addFieldset('submit_general', array('legend' => $this->__('Submission Fields')));

        $fieldset->addField('submission_action', 'hidden', array(
            'name'      => 'submission_action',
            'value'     => '1',
        ));

        switch ($deviceType) {
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE:
                $titleLength = Mage_XmlConnect_Helper_Iphone::SUBMISSION_TITLE_LENGTH;
                $descriptionLength = Mage_XmlConnect_Helper_Iphone::SUBMISSION_DESCRIPTION_LENGTH;
                $descriptionNote = $this->__('Description that appears in the iTunes App Store. %s chars maximum. ', $descriptionLength);
                break;
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPAD:
                $titleLength = Mage_XmlConnect_Helper_Ipad::SUBMISSION_TITLE_LENGTH;
                $descriptionLength = Mage_XmlConnect_Helper_Ipad::SUBMISSION_DESCRIPTION_LENGTH;
                $descriptionNote = $this->__('Description that appears in the iTunes App Store. %s chars maximum. ', $descriptionLength);
                break;
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_ANDROID:
                $titleLength = Mage_XmlConnect_Helper_Android::SUBMISSION_TITLE_LENGTH;
                $descriptionLength = Mage_XmlConnect_Helper_Android::SUBMISSION_DESCRIPTION_LENGTH;
                $descriptionNote = $this->__('Description that appears in Android Market. %s chars maximum. ', $descriptionLength);
                break;
        }

        $titleNote = $this->__('Name that appears beneath your app when users install it to their device. We recommend choosing a name that is 10-12 characters and that your customers will recognize. %s chars max.', $titleLength);

        $fieldset->addField('conf/submit_text/title', 'text', array(
            'name'      => 'conf[submit_text][title]',
            'label'     => $this->__('Title'),
            'maxlength' => $titleLength,
            'value'     => isset($formData['conf[submit_text][title]']) ? $formData['conf[submit_text][title]'] : null,
            'note'      => $titleNote,
            'required'  => true,
        ));

        if (isset($formData['conf[submit_text][description]'])) {
            $descrVal = $formData['conf[submit_text][description]'];
        } else {
            $descrVal = null;
        }

        $field = $fieldset->addField('conf/submit_text/description', 'textarea', array(
            'name'      => 'conf[submit_text][description]',
            'label'     => $this->__('Description'),
            'maxlength' => $descriptionLength,
            'value'     => $descrVal,
            'note'      => $descriptionNote,
            'required'  => true,
        ));
        $field->setRows(15);

        $fieldset->addField('conf/submit_text/contact_email', 'text', array(
            'name'      => 'conf[submit_text][email]',
            'label'     => $this->__('Contact Email'),
            'class'     => 'validate-email email',
            'maxlength' => '40',
            'value'     => isset($formData['conf[submit_text][email]']) ? $formData['conf[submit_text][email]'] : null,
            'note'      => $this->__('Administrative contact for this app and for app submission issues.'),
            'required'  => true,
        ));

        $fieldset->addField('conf/submit_text/price_free_label', 'label', array(
            'name'      => 'conf[submit_text][price_free_label]',
            'label'     => $this->__('Price'),
            'value'     => $this->__('Free'),
            'maxlength' => '40',
            'checked'   => 'checked',
            'note'      => $this->__('Only free apps are allowed in this version.'),
        ));

        $fieldset->addField('conf/submit_text/price_free', 'hidden', array(
            'name'      => 'conf[submit_text][price_free]',
            'value'     => '1',
        ));

        if (isset($formData['conf[submit_text][country]'])) {
            $selected = explode(',', $formData['conf[submit_text][country]']);
        } else {
            $selected = null;
        }

        $deviceHelper = Mage::helper('xmlconnect')->getDeviceHelper();
        $fieldset->addType('country', 'Mage_XmlConnect_Block_Adminhtml_Mobile_Form_Element_Country');
        $fieldset->addField('conf/submit_text/country', 'country', array(
            'id'                => 'submission-countries',
            'name'              => 'conf[submit_text][country][]',
            'label'             => $deviceHelper->getCountryLabel(),
            'values'            => Mage::helper('xmlconnect')->getCountryOptionsArray(),
            'value'             => $selected,
            'note'              => $this->__('Make this app available in the following territories'),
            'columns'           => $deviceHelper->getCountryColumns(),
            'place_name_left'   => $deviceHelper->isCountryNamePlaceLeft(),
            'class'             => $deviceHelper->getCountryClass(),
            'required'          => true,
        ))
        ->setRenderer($deviceHelper->getCountryRenderer());

        if (isset($formData['conf[submit_text][copyright]'])) {
            $copyVal = $formData['conf[submit_text][copyright]'];
        } else {
            $copyVal = null;
        }

        $fieldset->addField('conf/submit_text/copyright', 'text', array(
            'name'      => 'conf[submit_text][copyright]',
            'label'     => $this->__('Copyright'),
            'maxlength' => '200',
            'value'     => $copyVal,
            'note'      => $this->__('Appears in the info section of your app (example: Copyright 2010 – Your Company, Inc.)'),
            'required'  => true,
        ));

        if ($deviceType !== Mage_XmlConnect_Helper_Data::DEVICE_TYPE_ANDROID) {
            if (isset($formData['conf[submit_text][keywords]'])) {
                $keyWordsVal = $formData['conf[submit_text][keywords]'];
            } else {
                $keyWordsVal = null;
            }

            $fieldset->addField('conf/submit_text/keywords', 'text', array(
                'name'      => 'conf[submit_text][keywords]',
                'label'     => $this->__('Keywords'),
                'maxlength' => '100',
                'value'     => $keyWordsVal,
                'note'      => $this->__('One or more keywords that describe your app. Keywords are matched to users\' searches in the App Store and help return accurate search results. Separate multiple keywords with commas. 100 chars is maximum.'),
            ));
        }

        $fieldset = $form->addFieldset('submit_icons', array('legend' => $this->__('Icons')));

        switch ($deviceType) {
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPHONE:
                $this->addImage($fieldset, 'conf/submit/icon', $this->__('Large iTunes Icon'),
                    $this->__('Large icon that appears in the iTunes App Store. You do not need to apply a gradient or soft edges (this is done automatically by Apple).') . $this->__('Required size: %spx x %spx.', 1024, 1024), '', true);

                $this->addImage($fieldset, 'conf/submit/loader_image', $this->__('Loader Splash Screen'),
                    $this->__('Image that appears on first screen while your app is loading.') . $this->__('Required size: %spx x %spx.', 320, 460), '', true);

                $this->addImage($fieldset, 'conf/submit/loader_image_i4', $this->__('Loader Splash Screen (iPhone 4 retina)'),
                    $this->__('Image that appears on first screen while your app is loading.') . $this->__('Required size: %spx x %spx.', 640, 920), '', false);

                $this->addImage($fieldset, 'conf/submit/logo', $this->__('Custom App Icon'),
                    $this->__('Icon that will appear on the user\'s phone after they download your app.  You do not need to apply a gradient or soft edges (this is done automatically by Apple).') . $this->__('Recommended size: %spx x %spx at %s dpi.', 57, 57, 72), '', true);

                $this->addImage($fieldset, 'conf/submit/logo_i4', $this->__('Custom App Icon (iPhone 4 retina)'),
                    $this->__('Icon that will appear on the user\'s phone after they download your app. You do not need to apply a gradient or soft edges (this is done automatically by Apple).') . $this->__('Recommended size: %spx x %spx.', 114, 114), '', false);

                $this->addImage($fieldset, 'conf/submit/big_logo', $this->__('Copyright Page Logo'),
                    $this->__('Store logo that is displayed on copyright page of app.') . $this->__('Preferred size: %spx x %spx.', 100, 100), '', true);

                $this->addImage($fieldset, 'conf/submit/big_logo_i4', $this->__('Copyright Page Logo (iPhone 4 retina)'),
                    $this->__('Store logo that is displayed on copyright page of app.') . $this->__('Preferred size: %spx x %spx.', 200, 200), '', false);
                break;
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_IPAD:
                $this->addImage($fieldset, 'conf/submit/icon', $this->__('Large iTunes Icon'),
                    $this->__('Large icon that appears in the iTunes App Store. You do not need to apply a gradient or soft edges (this is done automatically by Apple).') . $this->__('Required size: %spx x %spx.', 1024, 1024), '', true);

                $this->addImage($fieldset, 'conf/submit/ipad_loader_portrait_image', $this->__('Loader Splash Screen (portrait mode)'),
                    $this->__('Image that appears on first screen while your app is loading.') . $this->__('Required size: %spx x %spx.', 768, 1024), '', true);

                $this->addImage($fieldset, 'conf/submit/ipad_loader_landscape_image', $this->__('Loader Splash Screen (landscape mode)'),
                    $this->__('Image that appears on first screen while your app is loading.') . $this->__('Required size: %spx x %spx.', 1024, 768), '', true);

                $this->addImage($fieldset, 'conf/submit/ipad_logo', $this->__('Custom App Icon'),
                    $this->__('Icon that will appear on the user\'s device after they download your app. You do not need to apply a gradient or soft edges (this is done automatically by Apple).') . $this->__('Recommended size: %spx x %spx.', 72, 72), '', true);

                $this->addImage($fieldset, 'conf/submit/big_logo', $this->__('Copyright Page Logo'),
                    $this->__('Store logo that is displayed on copyright page of app.') . $this->__('Preferred size: %spx x %spx.', 100, 100), '', true);
                break;
            case Mage_XmlConnect_Helper_Data::DEVICE_TYPE_ANDROID:
                $this->addImage($fieldset, 'conf/submit/icon', $this->__('High Resolution Application Icon'),
                    $this->__('The icon that appears in the Android Market.') . $this->__('Recommended size: %spx x %spx.', 512, 512) . $this->__('Maximum size: %s KB.', 1024), '', true);

                $this->addImage($fieldset, 'conf/submit/android_loader_image', $this->__('Loader Splash Screen'),
                    $this->__('Image that appears on first screen while your app is loading.') . $this->__('Required size: %spx x %spx.', 320, 455), '', true);

                $this->addImage($fieldset, 'conf/submit/android_logo', $this->__('Custom App Icon'),
                    $this->__('Icon that will appear on the user\'s device after they download your app.') . $this->__('Recommended size: %spx x %spx.', 48, 48), '', true);

                $this->addImage($fieldset, 'conf/submit/big_logo', $this->__('Copyright Page Logo'),
                    $this->__('Store logo that is displayed on copyright page of app.') . $this->__('Preferred size: %spx x %spx.', 100, 100), '', true);
                break;
        }

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Submission');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Submission');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Configure image element type
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('xmlconnect/adminhtml_mobile_helper_image'),
        );
    }

    /**
     * Prepare html output
     * Adding preview for images if application was submitted(so we have saved images)
     *
     * @return string
     */
    protected function _toHtml()
    {
        return parent::_toHtml() . $this->getChildHtml('images');
    }
}
