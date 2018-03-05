<?php

namespace Magecom\AroundProducts\Model\ResourceModel;


class AroundProducts extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    protected function _construct()
    {
        $this->_init('around_products', 'id');
    }

}
