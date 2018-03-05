<?php

namespace Magecom\AroundProducts\Model;

class ProductsSessionHistory extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'magecom_session_products_history';

    protected $_cacheTag = 'magecom_session_products_history';

    protected $_eventPrefix = 'magecom_session_products_history';

    protected function _construct()
    {
        $this->_init('Magecom\AroundProducts\Model\ResourceModel\ProductsSessionHistory');
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