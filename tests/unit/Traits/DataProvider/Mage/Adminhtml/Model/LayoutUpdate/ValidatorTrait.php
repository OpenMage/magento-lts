<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Traits\DataProvider\Mage\Adminhtml\Model\LayoutUpdate;

use Generator;
use Varien_Simplexml_Element;

trait ValidatorTrait
{
    public static function provideIsValidData(): Generator
    {
        yield 'string' => [
            true,
            'string',
        ];
        yield 'xml element' => [
            true,
            new Varien_Simplexml_Element('<root></root>'),
        ];
    }
}
