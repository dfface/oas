<?php

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Take extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $pk = ['stu_id','cou_id'];
}
