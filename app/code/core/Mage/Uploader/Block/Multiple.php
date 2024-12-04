<?php

/**
 * OpenMage
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/license/osl-3-0-php
 *
 * @category   Mage
 * @package    Mage_Uploader
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2019-2023 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * @category   Mage
 * @package    Mage_Uploader
 */
class Mage_Uploader_Block_Multiple extends Mage_Uploader_Block_Abstract
{
    public const DEFAULT_UPLOAD_BUTTON_ID_SUFFIX = 'upload';

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->_addElementIdsMapping([
            'upload' => $this->_prepareElementsIds([self::DEFAULT_UPLOAD_BUTTON_ID_SUFFIX])
        ]);

        $this->addButtons();
        return $this;
    }

    /**
     * @codeCoverageIgnore
     */
    protected function addButtons(): void
    {
        parent::addButtons();
        $this->setChild(self::BUTTON_UPLOAD, $this->getButtonUploadBlock());
    }

    public function getButtonUploadBlock(string $name = '', array $attributes = []): Mage_Adminhtml_Block_Widget_Button
    {
        return parent::getButtonUploadBlock($name, $attributes)
            ->setId($this->getElementId(self::DEFAULT_UPLOAD_BUTTON_ID_SUFFIX))
            ->setType('button');
    }
}
