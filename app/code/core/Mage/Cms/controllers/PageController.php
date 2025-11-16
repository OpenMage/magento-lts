<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Cms
 */

use Mage_Cms_Api_Data_PageInterface as PageInterface;

/**
 * CMS Page controller
 *
 * @package    Mage_Cms
 */
class Mage_Cms_PageController extends Mage_Core_Controller_Front_Action
{
    /**
     * View CMS page action
     *
     * @throws Mage_Core_Exception
     */
    public function viewAction()
    {
        $pageId = $this->getRequest()
            ->getParam(PageInterface::DATA_ID, $this->getRequest()->getParam('id', false));
        if (!Mage::helper('cms/page')->renderPage($this, $pageId)) {
            $this->_forward('noRoute');
        }
    }
}
