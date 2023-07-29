<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Product;

/**
 * Form actions for product page.
 */
class FormPageActions extends \Mage\Adminhtml\Test\Block\FormPageActions
{
    /**
     * "Save" button.
     *
     * @var string
     */
    protected $saveButton = '.scalable.save';

    /**
     * "Save" button.
     *
     * @var string
     */
    protected $saveAndContinueButton = '[onclick*="saveAndContinueEdit"]';

    /**
     * Selector for "Duplicate" button.
     *
     * @var string
     */
    protected $duplicateButton = 'button[onclick*="catalog_product/duplicate"]';

    /**
     * Header floating css selector.
     *
     * @var string
     */
    protected $headerFloating = '.content-header-floating';

    /**
     * Click on "Save" button.
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
     * Click on "Duplicate" button.
     *
     * @return void
     */
    public function duplicate()
    {
        $this->buttonClick('duplicate');
    }
}
