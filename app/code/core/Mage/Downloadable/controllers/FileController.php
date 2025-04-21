<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Downloadable
 */

require_once 'Mage/Downloadable/controllers/Adminhtml/Downloadable/FileController.php';

/**
 * Downloadable File upload controller
 *
 * @package    Mage_Downloadable
 * @deprecated  after 1.4.2.0 Mage_Downloadable_Adminhtml_Downloadable_FileController is used
 */
class Mage_Downloadable_FileController extends Mage_Downloadable_Adminhtml_Downloadable_FileController
{
    /**
     * Controller pre-dispatch method
     * Show 404 front page
     *
     * @return $this
     */
    public function preDispatch()
    {
        $this->_forward('defaultIndex', 'cms_index');

        return $this;
    }
}
