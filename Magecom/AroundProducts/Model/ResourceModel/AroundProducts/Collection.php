<?php
namespace Magecom\AroundProducts\Model\ResourceModel\AroundProducts;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'magecom_around_products';
    protected $_eventObject = 'around_products_collection';
    protected $_aliasIndex = 1;

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
        $this->eavConfig = $config;
        $this->eavAttr = $attribute;
        $buff = $this->joinEAV($this,'product_id_main','catalog_product','name', 'main_product_name');
        $this->addFilterToMap('main_product_name', $buff['table'].'.'.$buff['field']);
        $buff = $this->joinEAV($this,'product_id','catalog_product','name', 'product_name');
        $this->addFilterToMap('product_name', $buff['table'].'.'.$buff['field']);
        $buff = $this->joinEAV($this,'product_id_main','catalog_product','sku', 'main_product_sku');
        $this->addFilterToMap('main_product_sku', $buff['table'].'.'.$buff['field']);
        $buff = $this->joinEAV($this,'product_id','catalog_product','sku', 'product_sku');
        $this->addFilterToMap('product_sku', $buff['table'].'.'.$buff['field']);
    }

    public function joinEAV($collection, $mainTableForeignKey, $eavType, $attrCode, $fieldAlias, $mainTable = 'main_table'){
        $this->_aliasIndex++;
        $entityType = $this->eavConfig->getEntityType($eavType);
//        $entityTypeId = $entityType->getEntityTypeId();
        $entityTable = $collection->getTable($entityType->getEntityTable());
        //Use an incremented index to make sure all of the aliases for the eav attribute tables are unique.
        $attribute = $this->eavConfig->getAttribute($eavType, $attrCode);
        $attr =  $this->eavAttr->loadByCode($eavType, $attrCode);
        $alias = 'table_' . $this->_aliasIndex;
        $field = $attrCode; // This will either be the original attribute code or 'value'
        if ($attribute->getBackendType() != 'static'){
            $field = 'value';
            $table = $entityTable. '_'.$attribute->getBackendType();
            $collection->getSelect()
                ->joinLeft(array($alias => $table),
                    $mainTable . '.'.$mainTableForeignKey.' = '.$alias.'.entity_id and '.$alias.'.attribute_id = '. $attr->getId(),
                    array($fieldAlias => $alias . "." . $field)
                );
        }else{
            $collection->getSelect()
                ->joinLeft(array($alias => $entityTable),
                    $mainTable . '.'.$mainTableForeignKey.' = '. $alias.'.entity_id',
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
        $this->_init('Magecom\AroundProducts\Model\AroundProducts', 'Magecom\AroundProducts\Model\ResourceModel\AroundProducts');
    }

}