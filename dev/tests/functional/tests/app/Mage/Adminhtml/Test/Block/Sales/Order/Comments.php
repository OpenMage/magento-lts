<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Tests
 * @package    Tests_Functional
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
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
            if (str_contains($comment->getText(), $commentText)) {
                return true;
            }
        }
        return false;
    }
}
