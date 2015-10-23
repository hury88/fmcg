<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Goods;
use App\Models\HomeColumn;

/**
 * Created by PhpStorm.
 * User: Colin
 * Date: 2015/8/17
 * Time: 17:45
 */
class GoodsService
{

    /**
     * @param $data
     * @param $goods
     * @param bool|true $isWeb
     * @return array
     */
    static function getGoodsBySearch($data, $goods, $isWeb = true)
    {
        //排序
        if (isset($data['sort']) && in_array(strtolower($data['sort']), cons('goods.sort'))) {
            $goods->{'Order' . ucfirst($data['sort'])}();
        }
        // 省市县
        if (isset($data['province_id'])) {
            $goods->OfDeliveryArea($data);
        }

        $attrs = [];
        $resultCategories = [];
        $categories = Category::orderBy('level', 'asc')->with('icon')->select('name', 'level', 'id',
            'pid')->get()->toArray();
        if (isset($data['category_id'])) {
            //分类最高位为层级 后面为categoryId
            $level = substr($data['category_id'], 0, 1);
            $categoryId = substr($data['category_id'], 1);
            $resultCategories = CategoryService::formatCategoryForSearch($categories, $categoryId);

            $attrs = (new AttrService([]))->getAttrByCategoryId($categoryId);
            $goods->OfCategory($categoryId, $level);
        }

        // 标签
        if (isset($data['attr']) && !empty($data['attr'])) {
            $goods->OfAttr($data['attr']);
        }

        // 名称
        if (isset($data['name'])) {
            $goods->where('name', 'like', '%' . $data['name'] . '%')->get();

            $categoryIds = array_unique($goods->lists('cate_level_2')->toArray());
            $categories = array_filter($categories, function ($val) use ($categoryIds) {
                return in_array($val['id'], $categoryIds);
            });
        }


        $defaultAttrName = cons()->valueLang('attr.default');

        $searched = []; //保存已搜索的标签
        $moreAttr = []; //保存扩展的标签

        if ($isWeb) {
            // 已搜索的标签
            foreach ($attrs as $key => $attr) {
                if (!empty($data['attr']) && in_array($attr['attr_id'], array_keys($data['attr']))) {
                    $searched[$attr['attr_id']] = array_get($attr['child'], $data['attr'][$attr['attr_id']])['name'];
                    unset($attrs[$key]);
                } elseif (!in_array($attr['name'], $defaultAttrName)) {
                    $moreAttr[$key] = $attr;
                    unset($attrs[$key]);
                }
            }
        } else {
            //手机端有搜索名字时才返回
            $categories = isset($data['name']) ? $categories : new \stdClass();
        }
        return [
            'goods' => $goods,
            'attrs' => $attrs,
            'categories' => isset($data['category_id']) ? $resultCategories : $categories,
            'searched' => $searched,
            'moreAttr' => $moreAttr
        ];
    }

    /**
     * 获取首页商品栏目
     *
     * @return mixed
     */
    static function getGoodsColumn()
    {
        $type = auth()->user()->type;

        $columnTypes = cons('home_column.type');
        //商品
        $goodsColumns = HomeColumn::where('type', $columnTypes['goods'])->get();

        $goodsFields = [
            'id',
            'name',
            'price_retailer',
            'price_wholesaler',
            'is_new',
            'is_out',
            'is_promotion',
            'sales_volume'
        ];
        foreach ($goodsColumns as $goodsColumn) {
            $goods = Goods::whereIn('id', $goodsColumn->id_list)->where('user_type', '>',
                $type)->with('images')->select($goodsFields)->get();
            $columnGoodsCount = $goods->count();
            if ($columnGoodsCount < 10) {
                $columnGoodsIds = $goods->pluck('id')->toArray();
                $goodsBySort = Goods::whereNotIn('id', $columnGoodsIds)
                    ->where('user_type', '>', $type)
                    ->{'Of' . ucfirst(camel_case($goodsColumn->sort))}()
                    ->with('images')
                    ->select($goodsFields)
                    ->take(10 - $columnGoodsCount)
                    ->get();
                $goods = $goods->merge($goodsBySort);
            }
            $goodsColumn->goods = $goods;
        }

        return $goodsColumns;
    }

}