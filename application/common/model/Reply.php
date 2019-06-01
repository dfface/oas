<?php

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Reply extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $pk = 'id';
    // 关联方法，一个回复有且仅有一个用户 一对多
    public function user(){
        return $this->belongsTo('User','use_id','id');
    }
    // 关联方法，一个回复有且仅有一个问题 一对多
    public function question(){
        return $this->belongsTo('Question', 'que_id', 'id');
    }
    // 关联方法，一个a回复有零个或多个b回复，一个b回复仅对应一个a回复 一对多 注意回复表中必须有0号数据项，因为默认0为非二次回复
    public function replies(){
        return $this->hasMany('Reply','is_second_reply','id');
    }
}
