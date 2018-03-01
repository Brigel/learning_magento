<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magecom\AroundProducts\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\Data\ProductLinkInterface;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Eav\Api\AttributeSetRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product\Attribute\Source\Status;

/**
 * Class Related
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Around extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\Related
{
    const DATA_SCOPE_AROUND = 'around';

    /**
     * @var string
     */
    private static $previousGroup = 'search-engine-optimization';

    /**
     * @var int
     */
    private static $sortOrder = 90;


    /**
     * @var \Magento\Catalog\Ui\Component\Listing\Columns\Price
     */
    private $priceModifier;

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_RELATED => [
                    'children' => [
                        $this->scopePrefix . static::DATA_SCOPE_RELATED => $this->getRelatedFieldset(),
                        $this->scopePrefix . static::DATA_SCOPE_UPSELL => $this->getUpSellFieldset(),
                        $this->scopePrefix . static::DATA_SCOPE_CROSSSELL => $this->getCrossSellFieldset(),
                        $this->scopePrefix . static::DATA_SCOPE_AROUND => $this->getAroundFieldset(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Related Products, Up-Sells, Cross-Sells and Session products'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::DATA_SCOPE,
                                'sortOrder' =>
                                    $this->getNextGroupSortOrder(
                                        $meta,
                                        self::$previousGroup,
                                        self::$sortOrder
                                    ),
                            ],
                        ],

                    ],
                ],
            ]
        );

        return $meta;
    }


    /**
     * Get price modifier
     *
     * @return \Magento\Catalog\Ui\Component\Listing\Columns\Price
     * @deprecated
     */
    private function getPriceModifier()
    {
        if (!$this->priceModifier) {
            $this->priceModifier = ObjectManager::getInstance()->get(
                \Magento\Catalog\Ui\Component\Listing\Columns\Price::class
            );
        }
        return $this->priceModifier;
    }

    protected function getAroundFieldset()
    {
        $content = __(
            'An around item is offered to the customer as a pricier or higher-quality' .
            ' alternative to the product the customer is looking at.'
        );
        $res = [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Around Products'),
                    $this->scopePrefix . static::DATA_SCOPE_AROUND
                ),
                'modal' => $this->getGenericModal(
                    __('Add Around Products'),
                    $this->scopePrefix . static::DATA_SCOPE_AROUND
                ),
                static::DATA_SCOPE_AROUND => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_AROUND),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Around Products'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 20,
                    ],
                ],
            ]
        ];

        return $res;
    }
}
