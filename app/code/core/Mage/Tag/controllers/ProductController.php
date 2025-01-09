<?php

/**
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2020-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tagged products controller
 *
 * @category   Mage
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
