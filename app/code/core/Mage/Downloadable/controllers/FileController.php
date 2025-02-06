<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_Downloadable
 */

require_once 'Mage/Downloadable/controllers/Adminhtml/Downloadable/FileController.php';

/**
 * Downloadable File upload controller
 *
 * @category   Mage
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
