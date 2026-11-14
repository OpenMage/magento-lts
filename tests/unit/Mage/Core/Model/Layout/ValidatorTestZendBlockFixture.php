<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\Mage\Core\Model\Layout;

use Mage_Core_Block_Template_Zend;

/**
 * A block extending a block type that custom layout updates must not use
 */
final class ValidatorTestZendBlockFixture extends Mage_Core_Block_Template_Zend {}
