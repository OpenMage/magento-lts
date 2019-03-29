<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Tests
 * @package     Tests_Functional
 * @copyright  Copyright (c) 2006-2019 Magento, Inc. (http://www.magento.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Mage\Customer\Test\Block\Address;

use Mage\Customer\Test\Fixture\Address;

/**
 * Render output from Address fixture according to data format type.
 */
class Renderer
{
    /**
     * Address format type.
     *
     * @var string
     */
    protected $type;

    /**
     * Address fixture.
     *
     * @var Address
     */
    protected $address;

    /**
     * @constructor
     * @param Address $address
     * @param string $type
     */
    public function __construct(Address $address, $type = null)
    {
        $this->address = $address;
        $this->type = $type;
    }

    /**
     * Returns pattern according to address type.
     *
     * @return string
     */
    protected function getPattern()
    {
        $region = $this->resolveRegion();
        switch ($this->type) {
            case "html":
                $outputPattern = "{{depend}}{{prefix}} {{/depend}}{{firstname}} {{depend}}{{middlename}} {{/depend}}"
                    . "{{lastname}}{{depend}} {{suffix}}{{/depend}}\n{{depend}}{{company}}\n{{/depend}}{{street}}\n"
                    . "{{city}}, {{{$region}}}, {{postcode}}\n{{country_id}}\n{{depend}}T: {{telephone}}{{/depend}}"
                    . "{{depend}}\nF: {{fax}}{{/depend}}{{depend}}\nVAT: {{vat_id}}{{/depend}}";
                break;
            case "oneline":
            default:
                $outputPattern = "{{depend}}{{prefix}} {{/depend}}{{firstname}} {{depend}}{{middlename}} {{/depend}}"
                    . "{{lastname}}{{depend}} {{suffix}}{{/depend}}, {{street}}, "
                    . "{{city}}, {{{$region}}} {{postcode}}, {{country_id}}";
                break;
        }
        return $outputPattern;
    }

    /**
     * Render address according to format type.
     *
     * @return string
     */
    public function render()
    {
        $outputPattern = $this->getPattern();
        $fields = $this->getFieldsArray($outputPattern);
        $output = $this->preparePattern();
        $output = str_replace(['{{depend}}', '{{/depend}}', '{', '}'], '', $output);

        foreach ($fields as $field) {
            $data = $this->address->getData($field);
            $output = str_replace($field, $data, $output);
        }

        return $output;
    }

    /**
     * Get an array of necessary fields from pattern.
     *
     * @param string $outputPattern
     * @return mixed
     */
    protected function getFieldsArray($outputPattern)
    {
        $fieldsArray = [];
        preg_match_all('@\{\{(\w+)\}\}@', $outputPattern, $matches);
        foreach ($matches[1] as $item) {
            if ($item != 'depend') {
                $fieldsArray[] = $item;
            }
        }
        return $fieldsArray;
    }

    /**
     * Purge fields from pattern which are not present in fixture.
     *
     * @return string
     */
    protected function preparePattern()
    {
        $outputPattern = $this->getPattern();
        preg_match_all('@\{\{depend\}\}(.*?)\{\{.depend\}\}@siu', $outputPattern, $matches);
        foreach ($matches[1] as $key => $dependPart) {
            preg_match_all('@\{\{(\w+)\}\}@', $dependPart, $depends);
            foreach ($depends[1] as $depend) {
                if ($this->address->getData(trim($depend)) === null) {
                    $outputPattern = str_replace($matches[0][$key], "", $outputPattern);
                }
            }
        }
        return $outputPattern;
    }

    /**
     * Check necessary field to retrieve according to address country.
     *
     * @return string
     */
    protected function resolveRegion()
    {
        return $this->address->hasData('region') ? 'region' : 'region_id';
    }
}
