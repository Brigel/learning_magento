<?php

namespace Magecom\AroundProducts\Model;

class Product extends \Magento\Catalog\Model\Product
{

    public function getAroundProducts()
    {
        if (!$this->hasAroundProducts()) {
            $products = [];
            foreach ($this->getAroundProductCollection() as $product) {
                $products[] = $product;
            }
            $this->setAroundProducts($products);
        }
        return $this->getData('around_products');
    }

    /**
     * Retrieve up sell products identifiers
     *
     * @return array
     */
    public function getAroundProductIds()
    {
        if (!$this->hasAroundProductIds()) {
            $ids = [];
            foreach ($this->getAroundProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setAroundProductIds($ids);
        }
        return $this->getData('around_product_ids');
    }

    /**
     * Retrieve collection up sell product
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function getAroundProductCollection()
    {
        $collection = $this->getLinkInstance()->useAroundLinks()->getProductCollection()->setIsStrongMode();
        $collection->setProduct($this);
        return $collection;
    }

    /**
     * Retrieve collection up sell link
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Collection
     */
    public function getAroundLinkCollection()
    {
        $collection = $this->getLinkInstance()->useAroundLinks()->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }

}
