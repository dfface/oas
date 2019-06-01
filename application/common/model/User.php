<?php

namespace app\common\model;

use think\Model;
use think\model\concern\SoftDelete;

class User extends Model
{
    use SoftDelete;
    protected $deleteTime = 'delete_time';
    protected $pk = 'id';
    // 关联方法，一个学生有零个或多个课程 多对多
    public function stuCourses(){
        return $this->belongsToMany('Course', 'take', 'cou_id', 'stu_id');
    }
    // 关联方法，一个教师有零个或多个课程 一对多
    public function teaCourses(){
        return $this->hasMany('Course','tea_id','id');
    }
    // 关联方法，一个学生有零个或多个问题 一对多
    public function stuQuestions(){
        return $this->hasMany('Question', 'stu_id', 'id');
    }
    // 关联方法，一个用户有零个或多个回复 一对多
    public function replies(){
        return $this->hasMany('Reply', 'use_id', 'id');
    }
    // 关联方法，一个用户有零个或多个文件 一对多
    public function files(){
        return $this->hasMany('File', 'use_id', 'id');
    }
}
