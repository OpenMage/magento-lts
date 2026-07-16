<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Adminhtml
 */

/**
 * Wysiwyg controller for different purposes
 *
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Cms_WysiwygController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'cms';

    /**
     * Template directives callback
     *
     * TODO: move this to some model
     * @return void
     */
    public function directiveAction()
    {
        $directive = $this->getRequest()->getParam('___directive');
        $directive = Mage::helper('core')->urlDecode($directive);

        try {
            if (!preg_match(Varien_Filter_Template::CONSTRUCTION_PATTERN, $directive, $construction)
                || $construction[0] !== $directive
                || $construction[1] !== 'media'
            ) {
                Mage::throwException(Mage::helper('core')->__('Invalid media directive.'));
            }

            $url = Mage::getModel('cms/adminhtml_template_filter')->mediaDirective($construction);
            $image = Varien_Image_Adapter::factory('GD2');
            $image->open($url);
        } catch (Throwable) {
            $image = Varien_Image_Adapter::factory('GD2');
            $image->open(Mage::getSingleton('cms/wysiwyg_config')->getSkinImagePlaceholderPath());
        }

        $this->getResponse()->setHeader('Content-type', $image->getMimeTypeWithOutFileType());
        ob_start();
        $image->display();
        $this->getResponse()->setBody(ob_get_contents());
        ob_end_clean();
    }
}
