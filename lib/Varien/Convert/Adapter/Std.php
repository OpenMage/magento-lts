<?php

/**
 * @category   Varien
 * @package    Varien_Convert
 * @copyright  Copyright (c) 2006-2020 Magento, Inc. (https://www.magento.com)
 * @copyright  Copyright (c) 2022-2025 The OpenMage Contributors (https://www.openmage.org)
 * @license    https://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Convert STDIO adapter
 *
 * @category   Varien
 * @package    Varien_Convert
 */
class Varien_Convert_Adapter_Std extends Varien_Convert_Adapter_Abstract
{
    public function load()
    {
        $data = '';
        $stdin = fopen('php://STDIN', 'r');
        while ($text = fread($stdin, 1024)) {
            $data .= $text;
        }
        $this->setData($data);
        return $this;
    }

    public function save()
    {
        echo $this->getData();
        return $this;
    }
}
