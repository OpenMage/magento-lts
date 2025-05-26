<?php

declare(strict_types=1);

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Csp
 */

class Mage_Csp_Model_Observer_AddFrontendCspHeaders extends Mage_Csp_Model_Observer_Abstract
{
    /**
     * Add Content Security Policy headers to the frontend response
     */
    public function execute(Varien_Event_Observer $observer): void
    {
        $this->addCspHeaders($observer, Mage_Core_Model_App_Area::AREA_FRONTEND);
    }
}
