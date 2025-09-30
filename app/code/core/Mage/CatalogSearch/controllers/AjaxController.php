<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_CatalogSearch
 */

/**
 * Catalog Search Controller
 *
 * @package    Mage_CatalogSearch
 * @module     Catalog
 */
class Mage_CatalogSearch_AjaxController extends Mage_Core_Controller_Front_Action
{
    public function suggestAction()
    {
        if (!$this->getRequest()->getParam('q', false)) {
            $this->getResponse()->setRedirect(Mage::getSingleton('core/url')->getBaseUrl());
        }

        $this->getResponse()->setBody($this->getLayout()->createBlock('catalogsearch/autocomplete')->toHtml());
    }
}
