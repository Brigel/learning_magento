<?php

namespace Magecom\AroundProducts\Model;

class AroundProducts extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'magecom_around_products';

    protected $_cacheTag = 'magecom_around_products';

    protected $_eventPrefix = 'magecom_around_products';

    protected function _construct()
    {
        $this->_init('Magecom\AroundProducts\Model\ResourceModel\AroundProducts');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}