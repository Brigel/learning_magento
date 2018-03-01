<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magecom\AroundProducts\Controller\Adminhtml\Product\Initialization;

use Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory as CustomOptionFactory;
use Magento\Catalog\Api\Data\ProductLinkInterfaceFactory as ProductLinkFactory;
use Magento\Catalog\Api\ProductRepositoryInterface\Proxy as ProductRepository;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter;
use Magento\Catalog\Model\Product\Initialization\Helper\ProductLinks;
use Magento\Catalog\Model\Product\Link\Resolver as LinkResolver;
use Magento\Framework\App\ObjectManager;

/**
 * Class Helper
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Helper extends \Magento\Catalog\Controller\Adminhtml\Product\Initialization\Helper
{

    /**
     * @var LinkResolver
     */
    private $linkResolver;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\DateTime
     */
    private $dateTimeFilter;


    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        StockDataFilter $stockFilter,
        ProductLinks $productLinks,
        \Magento\Backend\Helper\Js $jsHelper,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
    ) {
        $this->dateTimeFilter = $dateFilter;
        parent::__construct($request, $storeManager, $stockFilter, $productLinks, $jsHelper, $dateFilter);
    }

    /**
     * Setting product links
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function setProductLinks(\Magento\Catalog\Model\Product $product)
    {
        $links = $this->getLinkResolver()->getLinks();

        $product->setProductLinks([]);

        $product = $this->productLinks->initializeLinks($product, $links);
        $productLinks = $product->getProductLinks();
        $linkTypes = [
            'related' => $product->getRelatedReadonly(),
            'upsell' => $product->getUpsellReadonly(),
            'crosssell' => $product->getCrosssellReadonly(),
            'around' => $product->getAroundReadonly()
        ];

        foreach ($linkTypes as $linkType => $readonly) {
            if (isset($links[$linkType]) && !$readonly) {
                foreach ((array)$links[$linkType] as $linkData) {
                    if (empty($linkData['id'])) {
                        continue;
                    }

                    $linkProduct = $this->getProductRepository()->getById($linkData['id']);
                    $link = $this->getProductLinkFactory()->create();
                    $link->setSku($product->getSku())
                        ->setLinkedProductSku($linkProduct->getSku())
                        ->setLinkType($linkType)
                        ->setPosition(isset($linkData['position']) ? (int)$linkData['position'] : 0);
                    $productLinks[] = $link;
                }
            }
        }

        return $product->setProductLinks($productLinks);
    }


    /**
     * @return CustomOptionFactory
     */
    private function getCustomOptionFactory()
    {
        if (null === $this->customOptionFactory) {
            $this->customOptionFactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Catalog\Api\Data\ProductCustomOptionInterfaceFactory');
        }
        return $this->customOptionFactory;
    }

    /**
     * @return ProductLinkFactory
     */
    private function getProductLinkFactory()
    {
        if (null === $this->productLinkFactory) {
            $this->productLinkFactory = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Catalog\Api\Data\ProductLinkInterfaceFactory');
        }
        return $this->productLinkFactory;
    }

    /**
     * @return ProductRepository
     */
    private function getProductRepository()
    {
        if (null === $this->productRepository) {
            $this->productRepository = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Catalog\Api\ProductRepositoryInterface\Proxy');
        }
        return $this->productRepository;
    }

    /**
     * @deprecated
     * @return LinkResolver
     */
    private function getLinkResolver()
    {
        if (!is_object($this->linkResolver)) {
            $this->linkResolver = ObjectManager::getInstance()->get(LinkResolver::class);
        }
        return $this->linkResolver;
    }

    /**
     * @return \Magento\Framework\Stdlib\DateTime\Filter\DateTime
     *
     * @deprecated
     */
    private function getDateTimeFilter()
    {
        if ($this->dateTimeFilter === null) {
            $this->dateTimeFilter = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Framework\Stdlib\DateTime\Filter\DateTime::class);
        }
        return $this->dateTimeFilter;
    }
}
