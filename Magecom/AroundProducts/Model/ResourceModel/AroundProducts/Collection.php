<?php

namespace Magecom\AroundProducts\Model\ResourceModel\AroundProducts;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'magecom_around_products';
    protected $_eventObject = 'around_products_collection';
    protected $_aliasIndex = 1;
    protected $_eavAttr;
    protected $_eavConfig;

    public function __construct(
        \Magento\Eav\Model\Config $config,
        \Magento\Eav\Model\Attribute $attribute,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->_eavConfig = $config;
        $this->_eavAttr = $attribute;
        $attributes = [
            [
                'main_table_foreign_key' => 'product_id_main',
                'eav_type' => 'catalog_product',
                'attr_code' => 'name',
                'field_alias' => 'main_product_name'
            ],
            [
                'main_table_foreign_key' => 'product_id',
                'eav_type' => 'catalog_product',
                'attr_code' => 'name',
                'field_alias' => 'product_name'
            ],
            [
                'main_table_foreign_key' => 'product_id_main',
                'eav_type' => 'catalog_product',
                'attr_code' => 'sku',
                'field_alias' => 'main_product_sku'
            ],
            [
                'main_table_foreign_key' => 'product_id',
                'eav_type' => 'catalog_product',
                'attr_code' => 'sku',
                'field_alias' => 'product_sku'
            ],
        ];
        $this->joinEavAttributesToTable($attributes);
    }

    public function joinEavAttributesToTable($attributes)
    {
        foreach ($attributes as $attributeData) {
            $buff = $this->joinEAV(
                $attributeData['main_table_foreign_key'],
                $attributeData['eav_type'],
                $attributeData['attr_code'],
                $attributeData['field_alias']);
            $this->addFilterToMap($attributeData['field_alias'], $buff['table'] . '.' . $buff['field']);
        }
    }

    public function joinEAV($mainTableForeignKey, $eavType, $attrCode, $fieldAlias, $mainTable = 'main_table')
    {
        $this->_aliasIndex++;
        $entityType = $this->_eavConfig->getEntityType($eavType);
        $entityTable = $this->getTable($entityType->getEntityTable());
        $attribute = $this->_eavConfig->getAttribute($eavType, $attrCode);
        $attr = $this->_eavAttr->loadByCode($eavType, $attrCode);
        $alias = 'table_' . $this->_aliasIndex;
        $field = $attrCode; // This will either be the original attribute code or 'value'
        if ($attribute->getBackendType() != 'static') {
            $field = 'value';
            $table = $entityTable . '_' . $attribute->getBackendType();
            $this->getSelect()
                ->joinLeft(array($alias => $table),
                    $mainTable . '.' . $mainTableForeignKey . ' = ' . $alias . '.entity_id and ' . $alias . '.attribute_id = ' . $attr->getId(),
                    array($fieldAlias => $alias . "." . $field)
                );
        } else {
            $this->getSelect()
                ->joinLeft(array($alias => $entityTable),
                    $mainTable . '.' . $mainTableForeignKey . ' = ' . $alias . '.entity_id',
                    array($fieldAlias => $alias . "." . $field)
                );
        }
        // Return the table alias and field name (either $attrCode or value) so we can use the table in future queries
        return array(
            "table" => $alias,
            "field" => $field
        );
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magecom\AroundProducts\Model\AroundProducts',
            'Magecom\AroundProducts\Model\ResourceModel\AroundProducts');
    }

}