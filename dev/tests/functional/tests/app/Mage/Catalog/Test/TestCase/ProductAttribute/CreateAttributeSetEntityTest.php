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
 * @copyright  Copyright (c) 2006-2015 X.commerce, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Catalog\Test\TestCase\ProductAttribute;

use Mage\Catalog\Test\Fixture\CatalogAttributeSet;
use Mage\Catalog\Test\Fixture\CatalogProductAttribute;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductSetAdd;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductSetEdit;
use Mage\Catalog\Test\Page\Adminhtml\CatalogProductSetIndex;
use Magento\Mtf\TestCase\Injectable;

/**
 * Precondition:
 * 1. Create new attribute.
 *
 * Steps:
 * 1. Log in to Backend.
 * 2. Navigate to Catalog > Attributes > Manage Attribute Sets.
 * 3. Start to create new Attribute Set.
 * 4. Fill out fields data according to data set.
 * 5. Add created Product Attribute to Attribute Set.
 * 6. Save new Attribute Set.
 * 7. Verify created Attribute Set.
 *
 * @group Product_Attributes_(CS)
 * @ZephyrId MPERF-6745
 */
class CreateAttributeSetEntityTest extends Injectable
{
    /**
     * Catalog Product Set page.
     *
     * @var CatalogProductSetIndex
     */
    protected $productSetIndex;

    /**
     * Catalog Product Set add page.
     *
     * @var CatalogProductSetAdd
     */
    protected $productSetAdd;

    /**
     * Catalog Product Set edit page.
     *
     * @var CatalogProductSetEdit
     */
    protected $productSetEdit;

    /**
     * Injection pages.
     *
     * @param CatalogProductSetIndex $productSetIndex
     * @param CatalogProductSetAdd $productSetAdd
     * @param CatalogProductSetEdit $productSetEdit
     * @return void
     */
    public function __inject(
        CatalogProductSetIndex $productSetIndex,
        CatalogProductSetAdd $productSetAdd,
        CatalogProductSetEdit $productSetEdit
    ) {
        $this->productSetIndex = $productSetIndex;
        $this->productSetAdd = $productSetAdd;
        $this->productSetEdit = $productSetEdit;
    }

    /**
     * Run CreateAttributeSetEntity test.
     *
     * @param CatalogAttributeSet $attributeSet
     * @param CatalogProductAttribute $productAttribute
     * @return void
     */
    public function test(CatalogAttributeSet $attributeSet, CatalogProductAttribute $productAttribute)
    {
        // Precondition
        $productAttribute->persist();

        // Steps:
        $this->productSetIndex->open();
        $this->productSetIndex->getPageActionsBlock()->addNew();

        $this->productSetAdd->getAttributeSetForm()->fill($attributeSet);
        $this->productSetAdd->getPageActions()->save();
        $this->productSetEdit->getAttributeSetEditBlock()->moveAttribute($productAttribute->getData());
        $this->productSetEdit->getPageActions()->save();
    }
}
