<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Mage_Adminhtml
 */
class Mage_Adminhtml_Catalog_DatafeedsController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction() {}

    /**
     * Check is allowed access to action
     *
     * @return true
     */
    protected function _isAllowed()
    {
        return true;
    }
}
