<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Rating
 */

/** @var Mage_Core_Model_Resource_Setup $this */
$installer = $this;

$data = [
    Mage_Rating_Model_Rating::ENTITY_PRODUCT_CODE       => [
        [
            'rating_code'   => 'Quality',
            'position'      => 0,
        ],
        [
            'rating_code'   => 'Value',
            'position'      => 0,
        ],
        [
            'rating_code'   => 'Price',
            'position'      => 0,
        ],
    ],
    Mage_Rating_Model_Rating::ENTITY_PRODUCT_REVIEW_CODE    => [
    ],
    Mage_Rating_Model_Rating::ENTITY_REVIEW_CODE            => [
    ],
];

foreach ($data as $entityCode => $ratings) {
    //Fill table rating/rating_entity
    $installer->getConnection()
        ->insert($installer->getTable('rating_entity'), ['entity_code' => $entityCode]);
    $entityId = $installer->getConnection()->lastInsertId($installer->getTable('rating_entity'));

    foreach ($ratings as $bind) {
        //Fill table rating/rating
        $bind['entity_id'] = $entityId;
        $installer->getConnection()->insert($installer->getTable('rating'), $bind);

        //Fill table rating/rating_option
        $ratingId = $installer->getConnection()->lastInsertId($installer->getTable('rating'));
        $optionData = [];
        for ($i = 1; $i <= 5; $i++) {
            $optionData[] = [
                'rating_id' => $ratingId,
                'code'      => (string) $i,
                'value'     => $i,
                'position'  => $i,
            ];
        }

        $installer->getConnection()->insertMultiple($installer->getTable('rating_option'), $optionData);
    }
}
