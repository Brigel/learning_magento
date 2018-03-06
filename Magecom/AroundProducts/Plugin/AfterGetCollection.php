<?php

namespace Magecom\AroundProducts\Plugin;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;

class AfterGetCollection
{
    protected $aroundProdsCollectionFactory;
    protected $registry;
    protected $productCollectionFactory;

    public function __construct(
        \Magecom\AroundProducts\Model\ResourceModel\AroundProducts\CollectionFactory $aroundProdsCollectionFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->registry = $registry;
        $this->aroundProdsCollectionFactory = $aroundProdsCollectionFactory;
        $this->productCollectionFactory = $productCollectionFactory;
    }

    public function afterCreateCollection(
        \Magento\CatalogWidget\Block\Product\ProductsList $subject,
        \Magento\Catalog\Model\ResourceModel\Product\Collection $collection
    ) {
        if (!$subject->getData('is_around')) {
            return $collection;
        }
        $product = $this->registry->registry('product');

        if (!isset($product) || empty($product) && !$product->getId()) {
            return $collection;
        }

        $aroundProdsCollection = $this->aroundProdsCollectionFactory->create();
        $aroundProdsCollection
            ->addFieldToFilter('product_id_main', ['eq' => $product->getId()])
            ->addFieldToFilter('is_disable', ['eq' => 0])
            ->getSelect()->limit($subject->getData('products_count'))
            ->order('main_table.counter DESC');

        $ids = [];
        foreach ($aroundProdsCollection->getItems() as $item) {
            array_push($ids, $item->getData('product_id'));
        }
        $collection->getSelect()->where('e.entity_id IN (?)', $ids);
        return $collection;
    }
}