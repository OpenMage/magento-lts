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
        try {
            $directive = (string) $this->getRequest()->getParam('___directive');
            $directive = Mage::helper('core')->urlDecode($directive);

            /** @var Mage_Cms_Model_Adminhtml_Template_Filter $filter */
            $filter = Mage::getModel('cms/adminhtml_template_filter');
            $url = $filter->getMediaFilePath($directive);
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
