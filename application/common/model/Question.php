<?php

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Question extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $pk = 'id';
    // 关联方法，一个问题有且仅有一个学生 一对多
    public function student(){
        return $this->belongsTo('User','stu_id','id');
    }
    // 关联方法，一个问题有零个或多个回复 一对多
    public function replies(){
        return $this->hasMany('Reply','que_id','id');
    }
}
