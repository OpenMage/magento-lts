<?php
/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2018-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

use Mage_Adminhtml_Block_Widget_Grid_Advanced_Abstract as AdvancedGrid;

/**
 * Grid widget massaction block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 *
 */
abstract class Mage_Adminhtml_Block_Widget_Grid_Advanced_Abstract extends Mage_Adminhtml_Block_Widget
{
    /**
     * Sets Advanced Gird template
     */
    public function __construct()
    {
        parent::__construct();
        //$this->setTemplate('widget/grid/advanced.phtml');
    }

    /**
     * Retrieve advanced block js object name
     *
     * @return mixed
     */
    public function getJsObjectName(): mixed
    {
        return $this->getHtmlId() . 'JsObject';
    }

    /**
     * Retrieve grid block js object name
     *
     * @return string
     */
    public function getGridJsObjectName(): string
    {
        return $this->getParentBlock()->getJsObjectName();
    }

    /**
     * Retrieve grid id
     *
     * @return mixed
     */
    public function getGridId(): mixed
    {
        return $this->getParentBlock()->getId();
    }

    /**
     * @return string
     */
    public function getJavaScript(): string
    {
        return sprintf(
            "var %s = new varienGridAdvanced('%s', %s, '%s')",
            $this->getJsObjectName(),
            $this->getGridId(),
            $this->getGridJsObjectName(),
            $this->getUrl('adminhtml/grid/saveColumnOrder')
        );
    }

    /**
     * Checks are advanced grid available
     *
     * @return bool
     */
    public function isAvailable(): bool
    {
        return $this->getParentBlock()->getHelperAdvancedGrid()->isEnabled();
    }
}
