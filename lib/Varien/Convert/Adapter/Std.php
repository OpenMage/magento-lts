<?php
/**
 * Convert STDIO adapter
 *
 * @copyright For copyright and license information, read the COPYING.txt file.
 * @link /COPYING.txt
 * @package    Varien_Convert
 */
/**
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
