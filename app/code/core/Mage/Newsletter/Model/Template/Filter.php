<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Newsletter
 */

/**
 * Template Filter Model
 *
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_Model_Template_Filter extends Mage_Widget_Model_Template_Filter
{
    /**
     * Generate widget HTML if template variables are assigned
     *
     * @param  array  $construction
     * @return string
     */
    public function widgetDirective($construction)
    {
        if (!isset($this->_templateVars['subscriber'])) {
            return $construction[0];
        }

        $construction[2] .= sprintf(' store_id ="%s"', $this->getStoreId());
        return parent::widgetDirective($construction);
    }
}
