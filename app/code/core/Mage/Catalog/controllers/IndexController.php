<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_Catalog
 */
class Mage_Catalog_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->_redirect('/');
    }
}
