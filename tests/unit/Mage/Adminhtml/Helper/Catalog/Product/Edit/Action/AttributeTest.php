<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Adminhtml\Helper\Catalog\Product\Edit\Action;

use Mage;
use Mage_Adminhtml_Helper_Catalog_Product_Edit_Action_Attribute as Subject;
use OpenMage\Tests\Unit\OpenMageTest;

final class AttributeTest extends OpenMageTest
{
    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::helper('adminhtml/catalog_product_edit_action_attribute');
    }
}
