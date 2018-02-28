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

namespace Mage\Adminhtml\Test\TestStep;

use Magento\Mtf\TestStep\TestStepInterface;
use Mage\Adminhtml\Test\Page\Adminhtml\StoreIndex;
use Mage\Adminhtml\Test\Page\Adminhtml\EditWebsite;
use Mage\Adminhtml\Test\Page\Adminhtml\DeleteWebsite;

/**
 * Delete all website, but default website.
 */
class DeleteWebsiteStep implements TestStepInterface
{
    /**
     * Websites' name array.
     *
     * @var array
     */
    protected $websitesNames;

    /**
     * Page store index.
     *
     * @var StoreIndex
     */
    protected $storeIndex;

    /**
     * Page edit website.
     *
     * @var EditWebsite
     */
    protected $editWebsite;

    /**
     * Page delete website.
     *
     * @var DeleteWebsite
     */
    protected $deleteWebsite;

    /**
     * @constructor
     * @param array $websitesNames
     * @param StoreIndex $storeIndex
     * @param EditWebsite $editWebsite
     * @param DeleteWebsite $deleteWebsite
     */
    public function __construct(
        array $websitesNames,
        StoreIndex $storeIndex,
        EditWebsite $editWebsite,
        DeleteWebsite $deleteWebsite
    ) {
        $this->websitesNames = $websitesNames;
        $this->storeIndex = $storeIndex;
        $this->editWebsite = $editWebsite;
        $this->deleteWebsite = $deleteWebsite;
    }

    /**
     * Delete websites.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->websitesNames as $websiteName) {
            $this->storeIndex->open();
            $this->storeIndex->getStoreGrid()->openWebsite($websiteName);
            $this->editWebsite->getFormPageActions()->delete();
            $this->deleteWebsite->getForm()->delete();
        }
    }
}
