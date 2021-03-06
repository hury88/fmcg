<?php

namespace App\Models;


class Attr extends Model
{
    protected $table = 'attr';
    public $timestamps = false;
    protected $primaryKey = 'attr_id';
    protected $fillable = ['name', 'attr_id', 'category_id', 'pid', 'status', 'sort'];

    /**
     * 分类表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    /**
     * 关联商品表
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function goods()
    {
        return $this->belongsToMany('App\Models\Goods', 'attr_goods', 'attr_id');
    }

    public function images()
    {
        return $this->belongsToMany('App\Models\images');
    }
}
