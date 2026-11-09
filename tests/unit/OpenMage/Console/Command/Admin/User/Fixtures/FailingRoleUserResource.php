<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console\Command\Admin\User\Fixtures;

use Mage_Admin_Model_Resource_User;
use Mage_Core_Model_Abstract;
use Override;
use RuntimeException;

/**
 * Saves the user as the core does and fails when its role is assigned
 */
final class FailingRoleUserResource extends Mage_Admin_Model_Resource_User
{
    public const MESSAGE = 'Test role assignment failure';

    #[Override]
    public function _saveRelations(Mage_Core_Model_Abstract $user): never
    {
        throw new RuntimeException(self::MESSAGE);
    }
}
