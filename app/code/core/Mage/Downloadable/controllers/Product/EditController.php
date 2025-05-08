<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

require_once 'Mage/Downloadable/controllers/Adminhtml/Downloadable/Product/EditController.php';

/**
 * Adminhtml downloadable product edit
 *
 * @package    Mage_Downloadable
 * @deprecated  after 1.4.2.0 Mage_Downloadable_Adminhtml_Downloadable_Product_EditController is used
 */
class Mage_Downloadable_Product_EditController extends Mage_Downloadable_Adminhtml_Downloadable_Product_EditController
{
    /**
     * Controller pre-dispatch method
     * Show 404 front page
     *
     * @return void
     */
    public function preDispatch()
    {
        $this->_forward('defaultIndex', 'cms_index');
    }
}
