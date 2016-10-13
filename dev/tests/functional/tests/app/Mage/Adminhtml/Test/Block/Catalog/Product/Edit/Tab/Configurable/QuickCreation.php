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

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Configurable;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Fixture\InjectableFixture;
use Mage\Adminhtml\Test\Block\Template;

/**
 * Quick creation form.
 */
class QuickCreation extends Form
{
    /**
     * Default fields for form.
     *
     * @var array
     */
    protected $defaultFields = ['name', 'sku'];

    /**
     * Selector for 'Quick Create' button.
     *
     * @var string
     */
    protected $create = '.save';

    /**
     * Backend abstract block selector.
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Create new product.
     *
     * @param InjectableFixture $product
     * @return void
     */
    public function create(InjectableFixture $product)
    {
        $this->fillDefaultField($product);
        $mapping = $this->dataMapping($product->getData());
        unset($mapping['isPersist']);
        $this->_fill($mapping);
        $this->_rootElement->find($this->create)->click();
        $this->getTemplateBlock()->waitLoader();
    }

    /**
     * Fill default fields.
     *
     * @param InjectableFixture $product
     * @return void
     */
    protected function fillDefaultField(InjectableFixture $product)
    {
        $data = [];
        foreach ($this->defaultFields as $field) {
            if ($product->hasData($field)) {
                $data[$field . '_autogenerate'] = 'No';
            }
        }
        $mapping = $this->dataMapping($data);
        $this->_fill($mapping);
    }

    /**
     * Get backend abstract block.
     *
     * @return Template
     */
    protected function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Mage\Adminhtml\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
