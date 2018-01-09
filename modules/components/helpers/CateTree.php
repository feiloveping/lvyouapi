<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/1
 * Time: 11:10
 */
namespace app\modules\components\helpers;

class CateTree
{
    public function make_tree1($list,$pk='id',$pid='pid',$child='son',$root=0){
        $tree=array();
        foreach($list as $key=> $val){
            if($val[$pid]==$root){
                unset($list[$key]);
                if(! empty($list)){
                    $son=$this->make_tree1($list,$pk,$pid,$child,$val[$pk]);
                    if(!empty($son)){
                        $val[$child]=$son;
                    }
                }
                $tree[]=$val;
            }
        }
        return $tree;
    }
}