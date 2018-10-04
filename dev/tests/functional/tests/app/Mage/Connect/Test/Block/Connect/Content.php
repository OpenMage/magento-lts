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

namespace Mage\Connect\Test\Block\Connect;

use Magento\Mtf\Block\Form;
use Mage\Connect\Test\Fixture\Connect;
use Mage\Admin\Test\Fixture\User;

/**
 * Form of 'Package' tab
 */
class Content extends Form
{
    /**
     * Title of channel
     *
     * @var string
     */
    protected $channelTitle = 'h2';

    /**
     * Button for verification available verions for upgrade
     *
     * @var string
     */
    protected $checkUpgradesButton = '.bar-head-btn button';

    /**
     * Label of available upgrades
     *
     * @var string
     */
    protected $upgradeAvailable = 'span[style="background:#fcfbbb;padding:0 5px;"]';

    /**
     * Button for download package
     *
     * @var string
     */
    protected $commitChangesButton = '.nm [type="button"]';

    /**
     * Frame of downloading progress
     *
     * @var string
     */
    protected $connectFrame = '#connect_iframe';

    /**
     * Locator of messages
     *
     * @var string
     */
    protected $message = '.msgs';

    /**
     * Get text of channel title
     *
     * @return string
     */
    public function getChannelTitle()
    {
        return $this->_rootElement->find($this->channelTitle)->getText();
    }

    /**
     * Click on the 'Check for Upgrades' button
     */
    public function checkForUpgrades()
    {
        $this->_rootElement->find($this->checkUpgradesButton)->click();
        $this->waitForElementVisible($this->upgradeAvailable);
    }

    /**
     * Select package for upgrade
     *
     * @param Connect $connect
     */
    public function selectPackages(Connect $connect)
    {
        $this->fill($connect);
    }

    /**
     * Start downloading
     */
    public function commitChanges()
    {
        $this->_rootElement->find($this->commitChangesButton)->click();
        $this->waitForElementVisible($this->connectFrame);
        $this->waitForElementVisible($this->message);
    }
}
