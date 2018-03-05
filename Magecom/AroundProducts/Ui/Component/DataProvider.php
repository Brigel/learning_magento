<?php
/**
 * Magecom
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magecom.net so we can send you a copy immediately.
 *
 * @category Magecom
 * @package Magecom_Module
 * @copyright Copyright (c) 2017 Magecom, Inc. (http://www.magecom.net)
 * @license  http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Magecom\AroundProducts\Ui\Component;


use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;

/**
 * Class DataProvider
 * @package Magecom\Brand\Ui\Component
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var \Magecom\Brand\Model\ResourceModel\Brand\Collection
     */
    protected $aroundProductsCollection;
    protected $productRepository;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        \Magecom\AroundProducts\Model\ResourceModel\AroundProducts\CollectionFactory $collectionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        array $meta = [],
        array $data = []
    ) {
        $collection = $collectionFactory->create();
        $this->aroundProductsCollection = $collection;
        $this->productRepository = $productRepository;
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    /**
     * @param $field
     * @param null $alias
     */
    public function addField($field, $alias = null)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $data = parent::getData();
        $items = $this->aroundProductsCollection->ToArray()['items'];
        $data['items'] = array_values($items);
        foreach ($data['items'] as &$item) {
            $mainProduct = $this->productRepository->getById($item['product_id_main']);
            $secondProduct = $this->productRepository->getById($item['product_id']);

            $item['main_product_name'] = $mainProduct->getName();
            $item['product_name'] = $secondProduct->getName();
            $item['main_product_sku'] = $mainProduct->getSku();
            $item['product_sku'] = $secondProduct->getSku();
        }
        unset($item);
        return $data;
    }
}