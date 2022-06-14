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
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Adminhtml\Test\Block\Admin;

use Magento\Mtf\Block\Form;
use Magento\Mtf\Client\Locator;
use Magento\Mtf\Client\Element\SimpleElement;
use Magento\Mtf\Block\BlockFactory;
use Magento\Mtf\Block\Mapper;
use Magento\Mtf\Client\BrowserInterface;
use Mage\Adminhtml\Test\Page\Adminhtml\Dashboard;
use Magento\Mtf\Util\ModuleResolver\SequenceSorterInterface;

/**
 * Login form for backend user.
 */
class Login extends Form
{
    /**
     * 'Log in' button.
     *
     * @var string
     */
    protected $submit = '[type=submit]';

    /**
     * Dashboard page.
     *
     * @var Dashboard
     */
    protected $dashboard;

    /**
     * @constructor
     * @param SimpleElement $element
     * @param BlockFactory $blockFactory
     * @param Mapper $mapper
     * @param BrowserInterface $browser
     * @param SequenceSorterInterface $sequenceSorter
     * @param array $config
     * @param Dashboard $dashboard
     */
    public function __construct(
        SimpleElement $element,
        BlockFactory $blockFactory,
        Mapper $mapper,
        BrowserInterface $browser,
        SequenceSorterInterface $sequenceSorter,
        array $config,
        Dashboard $dashboard
    ) {
        parent::__construct($element, $blockFactory, $mapper, $browser, $sequenceSorter, $config);
        $this->dashboard = $dashboard;
    }

    /**
     * Submit login form.
     *
     * @return void
     */
    protected function submit()
    {
        $this->_rootElement->find($this->submit, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Log in to admin panel.
     *
     * @param array $admin
     * @return void
     */
    public function loginToAdminPanel(array $admin)
    {
        $data = $this->dataMapping($admin);
        $this->_fill($data);
        $this->submit();
        if (!$this->_rootElement->isVisible()) {
            $this->dashboard->getAdminPanelHeader()->waitVisible();
        }
    }
}
