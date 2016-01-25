<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2016 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block;

/**
 * Form page actions block.
 */
class FormPageActions extends PageActions
{
    /**
     * 'Save' button selector.
     *
     * @var string
     */
    protected $saveButton = '.scalable.save';

    /**
     * "Save and Continue Edit" button.
     *
     * @var string
     */
    protected $saveAndContinueButton = '[onclick*="saveAndContinueEdit"]';

    /**
     * Header floating css selector.
     *
     * @var string
     */
    protected $headerFloating = '.content-header-floating';

    /**
     * 'Delete' button selector.
     *
     * @var string
     */
    protected $deleteButton = '.scalable.delete';

    /**
     * Selector for top page.
     *
     * @var string
     */
    protected $topPage = '#global_search';

    /**
     * Click "Save" button.
     *
     * @return void
     */
    public function save()
    {
        $this->buttonClick('save');
    }

    /**
     * Click on "Save and Continue Edit" button.
     *
     * @return void
     */
    public function saveAndContinue()
    {
        $this->buttonClick('saveAndContinue');
    }

    /**
     * Click "Delete" button.
     *
     * @return void
     */
    public function delete()
    {
        $this->_rootElement->find($this->deleteButton)->click();
    }

    /**
     * Click "Delete" button and accept alert.
     *
     * @return void
     */
    public function deleteAndAcceptAlert()
    {
        $this->delete();
        $this->browser->acceptAlert();
    }

    /**
     * Click on button.
     *
     * @param string $buttonName
     * @return void
     */
    protected function buttonClick($buttonName)
    {
        $this->clickOnTopPage();
        $button = $this->_rootElement->find($this->{$buttonName . 'Button'});
        if ($button->isVisible()) {
            $button->click();
        } else {
            $this->browser->find($this->headerFloating . ' ' . $this->{$buttonName . 'Button'})->click();
        }
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Click on top page.
     *
     * @return void
     */
    protected function clickOnTopPage()
    {
        $topPage = $this->browser->find($this->topPage);
        if ($topPage->isVisible()) {
            $topPage->click();
        }
    }
}
