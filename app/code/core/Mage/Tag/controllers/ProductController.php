<?php
/**
 * Tagged products controller
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Tag
 */
class Mage_Tag_ProductController extends Mage_Core_Controller_Front_Action
{
    public function listAction()
    {
        $tagId = $this->getRequest()->getParam('tagId');
        $tag = Mage::getModel('tag/tag')->load($tagId);

        if (!$tag->getId() || !$tag->isAvailableInStore()) {
            $this->_forward('404');
            return;
        }
        Mage::register('current_tag', $tag);

        $this->loadLayout();
        $this->_initLayoutMessages('checkout/session');
        $this->_initLayoutMessages('tag/session');
        $this->renderLayout();
    }
}
