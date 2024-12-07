<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Newsletter
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2024 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Template Filter Model
 *
 * @category   Mage
 * @package    Mage_Newsletter
 */
class Mage_Newsletter_Model_Template_Filter extends Mage_Widget_Model_Template_Filter
{
    /**
     * Generate widget HTML if template variables are assigned
     *
     * @param array $construction
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
