<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Tag
 */

/**
 * Tag Frontend controller
 *
 * @package    Mage_Tag
 */
class Mage_Tag_ListController extends Mage_Core_Controller_Front_Action
{
    /**
     * Tag list action
     *
     * @return void
     * @throws Mage_Core_Exception
     * @throws Mage_Core_Model_Store_Exception
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
