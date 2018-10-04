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
 * @copyright  Copyright (c) 2006-2018 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Install\Test\TestCase;

use Mage\Install\Test\Page\Downloader;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\ObjectManager;
use Magento\Mtf\System\Event\EventManagerInterface;

/**
 * PLEASE ADD NECESSARY INFO BEFORE RUNNING TEST TO ../dev/tests/functional/etc/config.xml
 *
 * Preconditions:
 * 1. Uninstall Magento.
 *
 * Steps:
 * 1. Opens Magento download page.
 * 2. Downloads magento
 * 3. Opens Magento Installation page
 *
 * @group Installer_and_Upgrade/Downgrade_(PS)
 */
class DownloaderPart2Test extends InstallTest
{
    /* tags */
    const TEST_TYPE = 'install';
    /* end tags */

    /**
     * Downloader page.
     *
     * @var Downloader
     */
    protected $downloaderDownloader;

    /**
     * Fixture factory.
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Injection data.
     *
     * @param Downloader $downloaderDownloader
     */
    public function __inject(Downloader $downloaderDownloader)
    {
        $this->downloaderDownloader = $downloaderDownloader;
    }

    /**
     * Install Magento via web interface.
     *
     * @param array $configData
     * @return array
     */
    public function test($configData)
    {
        // Steps:
        $this->downloaderDownloader->open();
        // Start downloading
        $this->downloaderDownloader->getContinueDownloadBlock()->startDownload();
        $i = 1;
        while ($i <= 15 and (!(($this->downloaderDownloader->getMessagesBlock()->isVisibleMessage('success')) or
                ($this->downloaderDownloader->getMessagesBlock()->isVisibleMessage('error'))))
        ) {
            sleep(60);
            $i++;
//            ObjectManager::getInstance()->create(EventManagerInterface::class)->dispatchEvent(array('exception'));
        }

        $this->downloaderDownloader->getMessagesBlock()->getSuccessMessages();
        $this->downloaderDownloader->getContinueDownloadBlock()->continueMagentoInstallation();
    }
}
