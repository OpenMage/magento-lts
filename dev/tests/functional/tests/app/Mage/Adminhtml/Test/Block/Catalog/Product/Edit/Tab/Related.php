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
 * @copyright  Copyright (c) 2006-2017 X.commerce, Inc. and affiliates (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab;

use Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Related\Grid as RelatedGrid;
use Magento\Mtf\Client\Element\SimpleElement as Element;

/**
 * Related Tab.
 */
class Related extends AbstractAppurtenant
{
    /**
     * Related products type.
     *
     * @var string
     */
    protected $type = 'related_products';

    /**
     * Locator for Related products grid.
     *
     * @var string
     */
    protected $relatedGrid = '#related_product_grid';

    /**
     * Get Related grid.
     *
     * @param Element|null $element [optional]
     * @return RelatedGrid
     */
    protected function getGrid(Element $element = null)
    {
        $element = $element ? $element : $this->_rootElement;
        return $this->blockFactory->create(
            '\Mage\Adminhtml\Test\Block\Catalog\Product\Edit\Tab\Related\Grid',
            ['element' => $element->find($this->relatedGrid)]
        );
    }
}
