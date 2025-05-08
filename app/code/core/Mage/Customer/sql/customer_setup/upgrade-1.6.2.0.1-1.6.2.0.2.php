<?php

/**
 * @copyright  For copyright and license information, read the COPYING.txt file.
 * @link       /COPYING.txt
 * @license    Open Software License (OSL 3.0)
 * @package    Mage_Customer
 */

/**
 * @var Mage_Catalog_Model_Resource_Setup $this
 * @var Varien_Db_Adapter_Interface $conn
 */
$conn = $this->getConnection();

//get all duplicated emails
$select  = $conn->select()
    ->from($this->getTable('customer/entity'), ['email', 'website_id', 'cnt' => 'COUNT(*)'])
    ->group('email')
    ->group('website_id')
    ->having('cnt > 1');
$emails = $conn->fetchAll($select);

foreach ($emails as $data) {
    $email = $data['email'];
    $websiteId = $data['website_id'];

    $select = $conn->select()
        ->from($this->getTable('customer/entity'), ['entity_id'])
        ->where('email = ?', $email)
        ->where('website_id = ?', $websiteId);
    $activeId = $conn->fetchOne($select);

    //receive all other duplicated customer ids
    $select = $conn->select()
        ->from($this->getTable('customer/entity'), ['entity_id', 'email'])
        ->where('email = ?', $email)
        ->where('website_id = ?', $websiteId)
        ->where('entity_id <> ?', $activeId);
    $result = $conn->fetchAll($select);

    //change email to unique value
    foreach ($result as $row) {
        $changedEmail = $conn->getConcatSql(['"(duplicate"', $row['entity_id'], '")"', '"' . $row['email'] . '"']);
        $conn->update(
            $this->getTable('customer/entity'),
            ['email' => $changedEmail],
            ['entity_id =?' => $row['entity_id']],
        );
    }
}

/**
 * Add unique index for customer_entity table
 */
$conn->addIndex(
    $this->getTable('customer/entity'),
    $this->getIdxName(
        'customer/entity',
        ['email', 'website_id'],
        Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
    ),
    ['email', 'website_id'],
    Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE,
);
