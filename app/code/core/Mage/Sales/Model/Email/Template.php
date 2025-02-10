<?php

/**
 * This file is part of OpenMage.
For copyright and license information, please view the COPYING.txt file that was distributed with this source code.
 *
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Sales
 */
class Mage_Sales_Model_Email_Template extends Mage_Core_Model_Email_Template
{
    /**
     * @param string $template
     * @return false|string
     */
    public function getInclude($template, array $variables)
    {
        $filename = Mage::getDesign()->getTemplateFilename($template);
        if (!$filename) {
            return '';
        }
        extract($variables, EXTR_SKIP);
        ob_start();
        include $filename;
        return ob_get_clean();
    }
}
