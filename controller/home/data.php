<?php

/*
 *
 *       _/_/_/                      _/        _/_/_/_/_/
 *    _/          _/_/      _/_/    _/  _/          _/      _/_/      _/_/
 *   _/  _/_/  _/_/_/_/  _/_/_/_/  _/_/          _/      _/    _/  _/    _/
 *  _/    _/  _/        _/        _/  _/      _/        _/    _/  _/    _/
 *   _/_/_/    _/_/_/    _/_/_/  _/    _/  _/_/_/_/_/    _/_/      _/_/
 *
 *
 *  Copyright 2013-2014, Geek Zoo Studio
 *  http://www.ecmobile.cn/license.html
 *
 *  HQ China:
 *    2319 Est.Tower Van Palace
 *    No.2 Guandongdian South Street
 *    Beijing , China
 *
 *  U.S. Office:
 *    One Park Place, Elmira College, NY, 14901, USA
 *
 *  QQ Group:   329673575
 *  BBS:        bbs.ecmobile.cn
 *  Fax:        +86-10-6561-5510
 *  Mail:       info@geek-zoo.com
 */

//include_once(EC_PATH . '/includes/init.php');

//include_once(EC_PATH . '/includes/lib_transaction.php');

require(EC_PATH . '/includes/init.php');
include_once(EC_PATH . '/includes/lib_transaction.php');

error_reporting(E_ALL);

$flash_arr = array();

function get_flash_xml_2()
{
    $flashdb = array();
    if (file_exists(ROOT_PATH . DATA_DIR . '/flash_data.xml')) {
        // 兼容v2.7.0及以前版本
        if (!preg_match_all('/item_url="([^"]+)"\slink="([^"]+)"\stext="([^"]*)"\ssort="([^"]*)"/', file_get_contents(ROOT_PATH . DATA_DIR . '/flash_data.xml'), $t, PREG_SET_ORDER)) {
            preg_match_all('/item_url="([^"]+)"\slink="([^"]+)"\stext="([^"]*)"/', file_get_contents(ROOT_PATH . DATA_DIR . '/flash_data.xml'), $t, PREG_SET_ORDER);
        }

        if (!empty($t)) {
            foreach ($t as $key => $val) {
                $val[4] = isset($val[4]) ? $val[4] : 0;
                $val[2] = substr($val[2], 0, 4) == 'http' ? $val[2] : dirname($GLOBALS['ecs']->url()) . '/' . $val[2];
                $flashdb[] = array('photo' => array('thumb' => API_DATA('PHOTO', $val[1]), 'img' => API_DATA('PHOTO', $val[1]), 'original' => API_DATA('PHOTO', $val[1])), 'url' => $val[2], 'description' => $val[3]);
            }
        }
    }
    return $flashdb;
}

$flash_arr['player'] = get_flash_xml_2();

$indexData = loadjson();
if(!empty($indexData))
{

    $goods_item_arr = array();
    foreach( $indexData->goodsitem as $index=> $item )
    {
        $line_item = array();
        if(!empty($item->title))
        $line_item["title"] = $item->title;
        $line_item['template'] = $item->tempname;

        foreach($item->item  as $key => $good_item )
        {
            $line_item['items'][$key] = get_goods_info_2($good_item);
        }

        $goods_item_arr[$index] = $line_item;
        $flash_arr['area'][] = $line_item;
        //print_r($item);
    }

    //print_r($goods_item_arr);

    $brand_item_arr = array();
    foreach( $indexData->branditem as $key => $item )
    {
        // $brand_item_arr[$key] = get_brand_info($item);
        $flash_arr['brand'][] =get_brand_info($item);

    }
    //print_r($brand_item_arr);

}

////print_r($indexData);
//
//$brand_tmp['name'] = "变形金刚";
//
//$brand_tmp['id'] = "6";
//$brand_tmp['desc'] = "《变形金刚》是从1984年起至今美国孩之宝公司与日本TAKARA公司合作开发的系列玩具和推出的系列动画片/影片的总称。";
//
//$brand_tmp['logo'] = "/data/brandlogo/1413512333052645922.jpg";
//$brand_tmp['site_url'] = "http://www.manluotuo.com/brand.php?id=6";
//
//
//$flash_arr['brand'][0] =$brand_tmp;
//$flash_arr['brand'][1] =$brand_tmp;
//$flash_arr['brand'][2] =$brand_tmp;
//$flash_arr['brand'][3] =$brand_tmp;
//$flash_arr['brand'][4] =$brand_tmp;
//$flash_arr['brand'][5] =$brand_tmp;
//
//$good_tmp['img'] = "";
//$good_tmp['id'] = 30;
//$good_tmp['type'] = 'goods';
//
//
////左一右二
//
//
//$area_tmp['title'] = "";
//$area_tmp['template'] = 'L1R2';
//$area_tmp['items'][0] = create_good_item();
//$area_tmp['items'][1] = create_good_item();
//$area_tmp['items'][2] = create_good_item();
//$flash_arr['area'][] = $area_tmp;
//
////左一右一
//$area_tmp_2['title'] = "";
//$area_tmp_2['template'] = 'L1R1';
//$area_tmp_2['items'][0] = create_good_item();
//$area_tmp_2['items'][1] = create_good_item();
//$flash_arr['area'][] = $area_tmp_2;
//
//
//
////左二右一
//
//$area_tmp_3['title'] = "";
//$area_tmp_3['template'] = 'L2R1';
//$area_tmp_3['items'][0] =  create_good_item();
//$area_tmp_3['items'][1] = create_good_item();
//$area_tmp_3['items'][2] = create_good_item();
//$flash_arr['area'][] = $area_tmp_3;



function create_good_item($title='',$img='',$tmplate=''){

    $good_tmp['img'] = "http://real-time.oss-cn-beijing.aliyuncs.com/images/201411/source_img/255_P_1414984871775.jpg";
    $good_tmp['id'] = 200;
    $good_tmp['type'] = 'goods';
    $good_tmp['name'] = '海贼王-弗兰奇将军';
    $good_tmp['price'] = '167';

    return $good_tmp;
}



function creat_area_item()
{

}




// url解析
function api_get_url($url)
{

    $out = array(
        'action' => '',
        'action_id' => 0
    );

    $site_url = dirname($GLOBALS['ecs']->url());

    if (strpos($url, $site_url) === false) {
        return $out;
    }

    if (strpos($url, '/goods.php') !== false) {
        $action = 'goods';
        $act_arr = explode('/goods.php', $url);
        if (strpos($act_arr[1], '?id=') !== false) {
            $action_id = ltrim($act_arr[1], '?id=');
        }
    } else if (strpos($url, '/category.php') !== false) {
        $action = 'category';
        $act_arr = explode('/category.php', $url);
        if (strpos($act_arr[1], '?id=') !== false) {
            $action_id = ltrim($act_arr[1], '?id=');
        }
    } else if (strpos($url, '/brand.php') !== false) {
        $action = 'brand';
        $act_arr = explode('/brand.php', $url);
        if (strpos($act_arr[1], '?id=') !== false) {
            $action_id = ltrim($act_arr[1], '?id=');
        }
    } else {
        return $out;
    }

    $out['action'] = $action;
    $out['action_id'] = (int)$action_id;

    return $out;
}

foreach ($flash_arr['player'] as $key => $val) {
    $action_info = api_get_url($val['url']);
    $flash_arr['player'][$key]['action'] = $action_info['action'];
    $flash_arr['player'][$key]['action_id'] = $action_info['action_id'];
}

$flash_arr['promote_goods'] = array();
// $best = get_recommend_goods('best');

$sales = gz_get_promote_goods();
if (count($sales) > 4) {
    $sales4 = array_slice($sales, 0, 4);
} else {
    $sales4 = $sales;
}

if (!empty($sales4)) {
    foreach ($sales4 as $key => $val) {
        $flash_arr['promote_goods'][] = array(
            'id' => $val['id'],
            'name' => $val['name'],
            'market_price' => $val['market_price'],
            'shop_price' => $val['shop_price'],
            'promote_price' => $val['promote_price'],
            'brief' => $val['brief'],
            'img' => array(
                'thumb' => API_DATA('PHOTO', $val['goods_thumb']),
                'img' => API_DATA('PHOTO', $val['goods_img']),
                'original' => API_DATA('PHOTO', $val['original_img'])
            )
        );
    }
}

function gz_get_promote_goods($cats = '')
{
    $time = gmtime();
    $order_type = $GLOBALS['_CFG']['recommend_order'];

    /* 取得促销lbi的数量限制 */
    $num = get_library_number("recommend_promotion");
    $sql = 'SELECT g.goods_id, g.goods_name, g.goods_name_style, g.market_price, g.shop_price AS org_price, g.promote_price, ' .
        "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS shop_price, " .
        "promote_start_date, promote_end_date, g.goods_brief, g.goods_thumb, goods_img, g.original_img, b.brand_name, " .
        "g.is_best, g.is_new, g.is_hot, g.is_promote, RAND() AS rnd " .
        'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
        'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON b.brand_id = g.brand_id ' .
        "LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp " .
        "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' " .
        'WHERE g.is_on_sale = 1 AND g.is_alone_sale = 1 AND g.is_delete = 0 ' .
        " AND g.is_promote = 1 AND promote_start_date <= '$time' AND promote_end_date >= '$time' ";
    $sql .= $order_type == 0 ? ' ORDER BY g.sort_order, g.last_update DESC' : ' ORDER BY rnd';
    $sql .= " LIMIT $num ";
    $result = $GLOBALS['db']->getAll($sql);

    $goods = array();
    foreach ($result AS $idx => $row) {
        if ($row['promote_price'] > 0) {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
            $goods[$idx]['promote_price'] = $promote_price > 0 ? price_format($promote_price) : '';
        } else {
            $goods[$idx]['promote_price'] = '';
        }

        $goods[$idx]['id'] = $row['goods_id'];
        $goods[$idx]['name'] = $row['goods_name'];
        $goods[$idx]['brief'] = $row['goods_brief'];
        $goods[$idx]['brand_name'] = $row['brand_name'];
        $goods[$idx]['goods_style_name'] = add_style($row['goods_name'], $row['goods_name_style']);
        $goods[$idx]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
        $goods[$idx]['short_style_name'] = add_style($goods[$idx]['short_name'], $row['goods_name_style']);
        $goods[$idx]['market_price'] = price_format($row['market_price']);
        $goods[$idx]['shop_price'] = price_format($row['shop_price']);
        $goods[$idx]['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
        $goods[$idx]['goods_img'] = get_image_path($row['goods_id'], $row['goods_img']);
        $goods[$idx]['original_img'] = get_image_path($row['goods_id'], $row['original_img']);
        $goods[$idx]['url'] = build_uri('goods', array('gid' => $row['goods_id']), $row['goods_name']);
    }

    return $goods;
}

GZ_Api::outPut($flash_arr);




function loadjson(){

    $json_content = "";
    $json_file = __DIR__ ."/json.php";


    // echo file_exists( __DIR__ ."/json.php");
    if(file_exists($json_file))
    {
        $json_content= file_get_contents($json_file);

        return json_decode($json_content);
    }else{
        return "";
    }
}

function get_brand_info($id)
{
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('brand') . " WHERE brand_id = '$id'";

    $row = $GLOBALS['db']->getRow($sql);

    if($row)
    {
        $brand_tmp['name'] = $row['brand_name'];

        $brand_tmp['id'] = $row["brand_id"];
        $brand_tmp['desc'] = $row['brand_desc'];

        $brand_tmp['logo'] = "/data/brandlogo/".$row['brand_logo'];
        //$brand_tmp['site_url'] =$row['site_url'];
        $brand_tmp['site_url'] ="http://www.manluotuo.com/brand-".$row['site_url']."-c0.html";
        return $brand_tmp;
    }else{
        return false;
    }
}

/**
 * 获得商品的详细信息
 *
 * @access  public
 * @param   integer     $goods_id
 * @return  void
 */
function get_goods_info_2($goods_id,$isMobile=false)
{
    $time = gmtime();
    $sql = 'SELECT g.*, c.measure_unit, b.brand_id, b.brand_name AS goods_brand, m.type_money AS bonus_money, ' .
        'IFNULL(AVG(r.comment_rank), 0) AS comment_rank, ' .
        "IFNULL(mp.user_price, g.shop_price * '$_SESSION[discount]') AS rank_price " .
        'FROM ' . $GLOBALS['ecs']->table('goods') . ' AS g ' .
        'LEFT JOIN ' . $GLOBALS['ecs']->table('category') . ' AS c ON g.cat_id = c.cat_id ' .
        'LEFT JOIN ' . $GLOBALS['ecs']->table('brand') . ' AS b ON g.brand_id = b.brand_id ' .
        'LEFT JOIN ' . $GLOBALS['ecs']->table('comment') . ' AS r '.
        'ON r.id_value = g.goods_id AND comment_type = 0 AND r.parent_id = 0 AND r.status = 1 ' .
        'LEFT JOIN ' . $GLOBALS['ecs']->table('bonus_type') . ' AS m ' .
        "ON g.bonus_type_id = m.type_id AND m.send_start_date <= '$time' AND m.send_end_date >= '$time'" .
        " LEFT JOIN " . $GLOBALS['ecs']->table('member_price') . " AS mp ".
        "ON mp.goods_id = g.goods_id AND mp.user_rank = '$_SESSION[user_rank]' ".
        "WHERE g.goods_id = '$goods_id' AND g.is_delete = 0 " .
        "GROUP BY g.goods_id";
    $row = $GLOBALS['db']->getRow($sql);

    if ($row !== false)
    {
        /* 用户评论级别取整 */
        $row['comment_rank']  = ceil($row['comment_rank']) == 0 ? 5 : ceil($row['comment_rank']);

        /* 获得商品的销售价格 */
        $row['market_price']        = price_format($row['market_price']);
        $row['shop_price_formated'] = price_format($row['shop_price']);

        /* 修正促销价格 */
        if ($row['promote_price'] > 0)
        {
            $promote_price = bargain_price($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
        }
        else
        {
            $promote_price = 0;
        }

        /* 处理商品水印图片 */
        $watermark_img = '';

        if ($promote_price != 0)
        {
            $watermark_img = "watermark_promote";
        }
        elseif ($row['is_new'] != 0)
        {
            $watermark_img = "watermark_new";
        }
        elseif ($row['is_best'] != 0)
        {
            $watermark_img = "watermark_best";
        }
        elseif ($row['is_hot'] != 0)
        {
            $watermark_img = 'watermark_hot';
        }

        if ($watermark_img != '')
        {
            $row['watermark_img'] =  $watermark_img;
        }

        $row['promote_price_org'] =  $promote_price;
        $row['promote_price'] =  price_format($promote_price);

        /* 修正重量显示 */
        $row['goods_weight']  = (intval($row['goods_weight']) > 0) ?
            $row['goods_weight'] . $GLOBALS['_LANG']['kilogram'] :
            ($row['goods_weight'] * 1000) . $GLOBALS['_LANG']['gram'];

        /* 修正上架时间显示 */
        $row['add_time']      = local_date($GLOBALS['_CFG']['date_format'], $row['add_time']);

        /* 促销时间倒计时 */
        $time = gmtime();
        if ($time >= $row['promote_start_date'] && $time <= $row['promote_end_date'])
        {
            $row['gmt_end_time']  = $row['promote_end_date'];
        }
        else
        {
            $row['gmt_end_time'] = 0;
        }

        /* 是否显示商品库存数量 */
        $row['goods_number']  = ($GLOBALS['_CFG']['use_storage'] == 1) ? $row['goods_number'] : '';

        /* 修正积分：转换为可使用多少积分（原来是可以使用多少钱的积分） */
        $row['integral']      = $GLOBALS['_CFG']['integral_scale'] ? round($row['integral'] * 100 / $GLOBALS['_CFG']['integral_scale']) : 0;

        /* 修正优惠券 */
        $row['bonus_money']   = ($row['bonus_money'] == 0) ? 0 : price_format($row['bonus_money'], false);

        /* 修正商品图片 */
        $row['goods_img']   = get_image_path($goods_id, $row['goods_img']);
        if($isMobile == false){
            $row['goods_thumb'] = get_image_path($goods_id, $row['goods_thumb'], true);
        } else{
            $row['goods_thumb'] =  $row['goods_img_mobile'];

        }

        $good_tmp['img'] = $row['goods_thumb'];
        $good_tmp['id'] = $row['goods_id'];
        $good_tmp['type'] = 'goods';
        $good_tmp['name'] = $row['goods_name'];
        $good_tmp['price'] = $row['shop_price'];




        return $good_tmp;
    }
    else
    {
        return false;
    }
}