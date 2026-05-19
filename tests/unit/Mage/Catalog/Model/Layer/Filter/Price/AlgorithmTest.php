<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Catalog\Model\Layer\Filter\Price;

// use Mage;
// use Mage_Catalog_Model_Layer_Filter_Price_Algorithm as Subject;
use Override;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Catalog\Model\Layer\Filter\Price\AlgorithmTrait;

final class AlgorithmTest extends OpenMageTest
{
    use AlgorithmTrait;

    // private static Subject $subject;

    #[Override]
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // self::$subject = Mage::getModel('catalog/layer_filter_price_algorithm');
        self::markTestSkipped('');
    }
}
