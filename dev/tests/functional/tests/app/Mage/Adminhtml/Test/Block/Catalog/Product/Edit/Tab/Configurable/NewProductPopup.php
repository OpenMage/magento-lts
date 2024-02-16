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

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable;

use Mage\Adminhtml\Test\Block\Catalog\Product\ProductForm;
use Mage\Adminhtml\Test\Block\Catalog\Product\FormPageActions;

/**
 * Product form on new product popup.
 */
class NewProductPopup extends ProductForm
{
    /**
     * Selector for form page actions block.
     *
     * @var string
     */
    protected $formPageActions = '.content-header';

    /**
     * Selector for success save message.
     *
     * @var string
     */
    protected $saveMessage = '.success-msg';

    /**
     * Get form page actions block.
     *
     * @return FormPageActions
     */
    public function getFormPageActions()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Catalog\Product\FormPageActions',
            ['element' => $this->_rootElement->find($this->formPageActions)]
        );
    }

    /**
     * Close popup.
     *
     * @return void
     */
    public function close()
    {
        $this->waitForElementVisible($this->saveMessage);
        $this->browser->closeWindow();
        $this->browser->selectWindow();
    }
}
