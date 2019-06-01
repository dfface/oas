<?php

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Course extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $pk = 'id';
    // 设置 自动类型转换
    protected $type = [
        'topic' => 'json',
    ];
    // 关联方法，一个课程有零个或多个学生
    public function students(){
        return $this->belongsToMany('User', 'take', 'stu_id', 'cou_id');
    }
    // 关联方法，一个课程有且仅有一个老师
    public function teacher(){
        return $this->belongsTo('User', 'tea_id', 'id');
    }
}
