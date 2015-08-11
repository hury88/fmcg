<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderGoods extends Model
{
    protected $table = 'order_goods';
    protected $fillable = [
        'goods_id',
        'price',
        'num',
        'total_price',
        'order_id'
    ];

    /**
     * 订单表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order');
    }

    /**
     * 商品表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function goods()
    {
        return $this->belongsTo('App\Models\Goods');
    }
}