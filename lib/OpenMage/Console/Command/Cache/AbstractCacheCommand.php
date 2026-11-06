<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    OpenMage_Console
 */

declare(strict_types=1);

namespace OpenMage\Console\Command\Cache;

use Override;
use Mage;
use Mage_Core_Model_App_Area;
use OpenMage\Console\Command\AbstractCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Cache commands ported from n98-magerun (https://github.com/netz98/n98-magerun), with the same names and options
 *
 * n98-magerun: Copyright (c) netz98 GmbH, MIT license, see lib/OpenMage/Console/LICENSE-n98-magerun.txt
 */
abstract class AbstractCacheCommand extends AbstractCommand
{
    protected ?string $eventArea = Mage_Core_Model_App_Area::AREA_ADMINHTML;

    #[Override]
    protected function configure(): void
    {
        $this
            ->addOption('reinit', null, InputOption::VALUE_NONE, 'Rebuild the configuration cache afterwards (default)')
            ->addOption('no-reinit', null, InputOption::VALUE_NONE, 'Do not rebuild the configuration cache afterwards');
    }

    protected function reinitConfig(InputInterface $input): void
    {
        if ($input->getOption('no-reinit') !== true) {
            Mage::app()->getConfig()->reinit();
        }
    }
}
