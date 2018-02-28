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

namespace Mage\Adminhtml\Test\Block\Sales\Order;

use Magento\Mtf\Block\Block;

/**
 * Order comments block.
 */
class Comments extends Block
{
    /**
     * Comment selector.
     *
     * @var string
     */
    protected $comment = '.note-list li';

    /**
     * Comments form selector.
     *
     * @var string
     */
    protected $commentsForm = '#history_form';

    /**
     * Get all comments elements.
     *
     * @return array
     */
    protected function getCommentsElements()
    {
        $this->waitForElementVisible($this->commentsForm);
        return $this->_rootElement->getElements($this->comment);
    }

    /**
     * Check if comment with specific text is present.
     *
     * @param string $commentText
     * @return bool
     */
    public function isCommentPresent($commentText)
    {
        $comments = $this->getCommentsElements();
        foreach ($comments as $comment) {
            if (strpos($comment->getText(), $commentText) !== false) {
                return true;
            }
        }
        return false;
    }
}
