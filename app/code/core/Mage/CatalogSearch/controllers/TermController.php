<?php
/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @license Open Software License (OSL 3.0)
 * @package    Mage_CatalogSearch
 */
class Mage_CatalogSearch_TermController extends Mage_Core_Controller_Front_Action
{
    /**
     * @return $this|Mage_Core_Controller_Front_Action
     */
    public function preDispatch()
    {
        parent::preDispatch();
        if (!Mage::getStoreConfig('catalog/seo/search_terms')) {
            $this->_redirect('noroute');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }
        return $this;
    }
    public function popularAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
