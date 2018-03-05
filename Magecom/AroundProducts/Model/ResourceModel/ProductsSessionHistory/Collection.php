<?php
namespace Magecom\AroundProducts\Model\ResourceModel\ProductsSessionHistory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'magecom_session_products_history';
    protected $_eventObject = 'magecom_session_products_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magecom\AroundProducts\Model\ProductsSessionHistory', 'Magecom\AroundProducts\Model\ResourceModel\ProductsSessionHistory');
    }

}