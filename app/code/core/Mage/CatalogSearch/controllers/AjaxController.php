<?php

/**
 * This file is part of OpenMage.
 * For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @category   Mage
 * @package    Mage_CatalogSearch
 */

/**
 * Catalog Search Controller
 *
 * @category   Mage
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
