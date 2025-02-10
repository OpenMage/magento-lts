<?php
/**
 * Controller for CMS Block Widget plugin
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Cms_Block_WidgetController extends Mage_Adminhtml_Controller_Action
{
    /**
     * ACL resource
     * @see Mage_Adminhtml_Controller_Action::_isAllowed()
     */
    public const ADMIN_RESOURCE = 'cms/widget_instance';

    /**
     * Chooser Source action
     */
    public function chooserAction()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $pagesGrid = $this->getLayout()->createBlock('adminhtml/cms_block_widget_chooser', '', [
            'id' => $uniqId,
        ]);
        $this->getResponse()->setBody($pagesGrid->toHtml());
    }
}
