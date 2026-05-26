<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Entity\Product\Attribute\Design\Options;

// use Mage;
// use Mage_Catalog_Model_Entity_Product_Attribute_Design_Options_Container as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Entity\Product\Attribute\Design\Options\ContainerTrait;

final class ContainerTest extends OpenMageTest
{
    use ContainerTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('catalog/entity_product_attribute_design_options_container');
        self::markTestSkipped('');
    }
}
