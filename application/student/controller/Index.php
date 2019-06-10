<?php


namespace app\student\controller;


use app\common\model\Question;
use app\common\model\Reply;
use think\Controller;
use think\facade\Session;

class Index extends Controller
{
    /**
     * Method roleCheck
     * @purpose 学生用户身份检查
     * @return bool
     */
    public function roleCheck() {
        // 注意 null 也解释为0
        if (Session::get('role') === ROLE_STUDENT) {
            return true;
        }
        else {
            $this->error('身份验证失败！','index/Index/index');
        }
    }

    /**
     * Method index
     * @purpose 学生主页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index() {
        $this->roleCheck();
        $id = Session::get('id');
        $solved_num = Question::where('stu_id',$id)->where('status','1')->select()->count();
        $asked_num = Question::where('stu_id', $id)->count();
        $this->assign('solved_num', $solved_num);
        $this->assign('asked_num', $asked_num);
        return $this->fetch();
    }

    /**
     * Method  questionAsk
     * @purpose 提问主页
     * @return mixed
     */
    public function questionAsk() {
        $this->roleCheck();
        return $this->fetch();
    }

    /**
     * Method questionSubmit
     * @purpose 问题提交写入数据库接口
     * @return \think\response\Json
     */
    public function questionSubmit() {
        $this->roleCheck();
        $question = new Question();
        $question->stu_id = $this->request->param('stu_id');
        $question->course_id = $this->request->param('course_id');
        $question->title = $this->request->param('title');
        $question->content = $this->request->param('content');
        if($question->save()){
            $result = ['code' => SUCCESS, 'msg' => "提交成功", 'data' => $question->id];
        }
        else{
            $result = ['code' => FAILURE, 'msg' => "提交失败，请待会儿重试"];
        }
        return json($result);
    }

    /**
     * Method adopt
     * @purpose 学生采纳答案
     * @return \think\response\Json
     */
    public function adopt() {
        $this->roleCheck();
        $rep_id = $this->request->param('rep_id');
        $reply  = Reply::get($rep_id);
        $reply->status = 1;
        $reply->save();
        $que_id = $this->request->param('que_id');
        $question  = Question::get($que_id);
        $question->status = 1;
        $question->save();
        $result = ['code' => SUCCESS, 'msg' => "成功"];
        return json($result);
    }

    /**
     * Method questionDelete
     * @purpose 学生对问题的删除接口
     * @params id question_id
     * @return \think\response\Json
     */
    public function questionDelete() {
        $this->roleCheck();
        $stu_id = session('id');
        $que_id = $this->request->param('question_id');
        // 问题的属主验证
        $question = Question::get($que_id);
        if ( $question->stu_id == $stu_id) {
            $question->delete();
            $result = ["code" => SUCCESS, "msg" => "成功删除!"];
        }
        else {
            $result = ["code" => FAILURE, "msg" => "没有删除权限!"];
        }
        return json($result);
    }

}