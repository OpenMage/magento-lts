<?php
/**
 * CMS Page controller
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Cms
 */
class Mage_Cms_PageController extends Mage_Core_Controller_Front_Action
{
    /**
     * View CMS page action
     *
     */
    public function viewAction()
    {
        $pageId = $this->getRequest()
            ->getParam('page_id', $this->getRequest()->getParam('id', false));
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('noRoute');
        }
    }
}
