<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Bundle\Model\Source\Option\Selection\Price;

use Mage;
use Mage_Bundle_Model_Source_Option_Selection_Price_Type as Subject;
use OpenMage\Tests\Unit\OpenMageTest;
use OpenMage\Tests\Unit\Traits\DataProvider\Mage\Bundle\Model\Source\Option\Selection\Price\TypeTrait;

final class TypeTest extends OpenMageTest
{
    use TypeTrait;

    private static Subject $subject;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::$subject = Mage::getModel('bundle/source_option_selection_price_type');
    }
}
