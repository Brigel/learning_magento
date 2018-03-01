<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magecom\AroundProducts\Model\Product;

class Link extends \Magento\Catalog\Model\Product\Link
{

    const LINK_TYPE_AROUND = 20;

    /**
     * @return $this
     */
    public function useAroundLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_AROUND);
        return $this;
    }

}
