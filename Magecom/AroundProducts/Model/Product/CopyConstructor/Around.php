<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magecom\AroundProducts\Model\Product\CopyConstructor;

class Around implements \Magento\Catalog\Model\Product\CopyConstructorInterface
{
    /**
     * Build product links
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Product $duplicate
     * @return void
     */
    public function build(\Magento\Catalog\Model\Product $product, \Magento\Catalog\Model\Product $duplicate)
    {
        $data = [];
        $link = $product->getLinkInstance();
        $link->useAroundLinks();
        $attributes = [];
        foreach ($link->getAttributes() as $attribute) {
            if (isset($attribute['code'])) {
                $attributes[] = $attribute['code'];
            }
        }
        /** @var \Magento\Catalog\Model\Product\Link $link  */
        foreach ($product->getAroundLinkCollection() as $link) {
            $data[$link->getLinkedProductId()] = $link->toArray($attributes);
        }
        $duplicate->setAroundLinkData($data);
    }
}
