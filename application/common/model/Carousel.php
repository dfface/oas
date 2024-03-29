<?php


namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class Carousel extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $pk = 'id';
}