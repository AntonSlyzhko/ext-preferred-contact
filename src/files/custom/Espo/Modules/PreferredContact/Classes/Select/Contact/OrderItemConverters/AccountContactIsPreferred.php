<?php

namespace Espo\Modules\PreferredContact\Classes\Select\Contact\OrderItemConverters;

use Espo\Core\Select\Order\Item;
use Espo\Core\Select\Order\ItemConverter;
use Espo\ORM\Query\Part\Expression as Expr;
use Espo\ORM\Query\Part\Order;
use Espo\ORM\Query\Part\OrderList;

/**
 * @noinspection PhpUnused
 */
class AccountContactIsPreferred implements ItemConverter
{
    public function convert(Item $item): OrderList
    {
        return OrderList::create([
            Order
                ::create(Expr::column('accountContact.pcIsPreferred'))
                ->withDirection($item->getOrder()),
            Order::create(Expr::column('firstName')),
            Order::create(Expr::column('lastName'))
        ]);
    }
}
