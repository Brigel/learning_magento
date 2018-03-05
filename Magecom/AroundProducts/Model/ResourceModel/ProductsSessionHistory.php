<?php

namespace Magecom\AroundProducts\Model\ResourceModel;


class ProductsSessionHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('products_session_history', 'id');
    }

}
