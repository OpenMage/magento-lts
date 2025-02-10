<?php
/**
 * Customer reviews controller
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Customer
 */
class Mage_Customer_ReviewController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
