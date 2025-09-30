<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Varien_Convert
 */

/**
 * Convert STDIO adapter
 *
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
