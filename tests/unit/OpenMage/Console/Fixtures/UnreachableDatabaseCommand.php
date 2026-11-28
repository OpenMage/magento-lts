<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Tests
 */

declare(strict_types=1);

namespace OpenMage\Tests\Unit\OpenMage\Console\Fixtures;

use Mage_Core_Model_App;
use OpenMage\Console\Command\AbstractCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Zend_Db_Adapter_Exception;

/**
 * Fails to start the application as Mage::app() does when the database server refuses the connection
 */
final class UnreachableDatabaseCommand extends AbstractCommand
{
    public const DRIVER_MESSAGE = 'SQLSTATE[HY000] [2002] Connection refused';

    protected function initApp(string $store): Mage_Core_Model_App
    {
        throw new Zend_Db_Adapter_Exception(self::DRIVER_MESSAGE, 2002);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return Command::SUCCESS;
    }
}
