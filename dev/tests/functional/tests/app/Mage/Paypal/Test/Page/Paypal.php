<?php
/**
 * OpenMage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Paypal\Test\Page;

use Magento\Mtf\Page\Page;
use Mage\Paypal\Test\Block;
use Mage\Paypal\Test\Block\Login;
use Mage\Paypal\Test\Block\Review;
use Mage\Paypal\Test\Block\OldReview;
use Mage\Paypal\Test\Block\OldLogin;

/**
 * Pay Pal page.
 */
class Paypal extends Page
{
    /**
     * Page url.
     */
    const MCA = 'paypal';

    /**
     * Blocks' config
     *
     * @var array
     */
    protected $blocks = [
        'loginBlock' => [
            'class' => 'Mage\Paypal\Test\Block\Login',
            'locator' => '#login', // previous locator = #contents
            'strategy' => 'css selector',
        ],
        'oldLoginBlock' => [
            'class' => 'Mage\Paypal\Test\Block\OldLogin',
            'locator' => '#loginModule',
            'strategy' => 'css selector',
        ],
        'reviewBlock' => [
            'class' => 'Mage\Paypal\Test\Block\Review',
            'locator' => '.outerWrapper',
            'strategy' => 'css selector',
        ],
        'oldReviewBlock' => [
            'class' => 'Mage\Paypal\Test\Block\OldReview',
            'locator' => '#content',
            'strategy' => 'css selector',
        ],
        'newLoginBlock' => [
            'class' => 'Mage\Paypal\Test\Block\NewLogin',
            'locator' => '#login',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * Custom initialization.
     *
     * @return void
     */
    protected function _init()
    {
        $this->_url = 'https://www.sandbox.paypal.com/cgi-bin/';
    }

    /**
     * @return Login
     */
    public function getLoginBlock()
    {
        return $this->getBlockInstance('loginBlock');
    }

    /**
     * @return Review
     */
    public function getReviewBlock()
    {
        return $this->getBlockInstance('reviewBlock');
    }

    /**
     * @return OldLogin
     */
    public function getOldLoginBlock()
    {
        return $this->getBlockInstance('oldLoginBlock');
    }

    /**
     * @return OldReview
     */
    public function getOldReviewBlock()
    {
        return $this->getBlockInstance('oldReviewBlock');
    }

    /**
     * @return NewLogin
     */
    public function getNewLoginBlock()
    {
        return $this->getBlockInstance('newLoginBlock');
    }
}
