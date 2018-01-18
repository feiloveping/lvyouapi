<?php
/**
 * Created by PhpStorm.
 * User: 张鹏飞
 * Date: 2017/12/22
 * Time: 11:14
 */

namespace app\modules\components\helpers;

class FeiArr
{
    public function array_sort($arr,$keys,$type='asc'){
        $keysvalue = $new_array = array();
        foreach ($arr as $k=>$v){
            $keysvalue[$k] = $v[$keys];
        }
        if($type == 'asc'){
            asort($keysvalue);
        }elseif($type == 'desc'){
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k=>$v){
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }


    public function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){
        if(is_array($arrays)){
            foreach ($arrays as $array){
                if(is_array($array)){
                    $key_arrays[] = $array[$sort_key];
                }else{
                    return false;
                }
            }
        }else{
            return false;
        }
        array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
        return $arrays;
    }

    public function pageArr($arr,$page,$pagesize=6)
    {
        $page = (int)$page;
        $pagesize = (int)$pagesize;
        $pagecount = ceil(count($arr) / $pagesize);
        if($page<1) $page = 1;
        if($page > $pagecount) $page = $pagecount;
        $offset = $pagesize * ($page - 1 );
        if($page < $pagecount)
        {
            $limit = $pagesize;
        }else{
            $limit = count($arr) % $pagesize;
        }
        $data = array_slice($arr,$offset ,$limit);
        if(empty($data))
            return false;
        else
            return ['pagecount'=>$pagecount,'data'=>$data];
    }
}