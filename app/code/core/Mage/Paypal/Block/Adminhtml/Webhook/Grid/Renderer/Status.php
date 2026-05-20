<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Paypal
 */

declare(strict_types=1);

class Mage_Paypal_Block_Adminhtml_Webhook_Grid_Renderer_Status extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    private const STATUS_CLASSES = [
        Mage_Paypal_Model_Webhook_Event::STATUS_PROCESSED => 'grid-severity-notice',
        Mage_Paypal_Model_Webhook_Event::STATUS_IGNORED   => 'grid-severity-minor',
        Mage_Paypal_Model_Webhook_Event::STATUS_FAILED    => 'grid-severity-critical',
        Mage_Paypal_Model_Webhook_Event::STATUS_DEFERRED  => 'grid-severity-major',
        Mage_Paypal_Model_Webhook_Event::STATUS_DUPLICATE => 'grid-severity-minor',
    ];

    /**
     * Render a status badge.
     */
    #[Override]
    public function render(Varien_Object $row): string
    {
        $status = (string) $row->getData($this->getColumn()->getIndex());
        $class = self::STATUS_CLASSES[$status] ?? 'grid-severity-notice';

        return sprintf(
            '<span class="%s"><span>%s</span></span>',
            $this->escapeHtml($class),
            $this->escapeHtml(ucwords(str_replace('_', ' ', $status))),
        );
    }
}
