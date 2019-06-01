<?php

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class File extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $pk = 'id';
    // 关联方法，一个文件有且仅有一个用户 一对多
    public function user(){
        return $this->belongsTo('User', 'use_id', 'id');
    }
}
