<?php


namespace app\teacher\controller;

use app\common\model\Question;
use app\common\model\Course;
use app\common\model\Reply;
use app\common\model\Take;
use app\common\model\User;
use think\Controller;
use think\facade\Session;

class Index extends Controller
{
    /**
     * Method roleCheck
     * @purpose 教师用户身份检查
     * @return bool
     */
    public function roleCheck() {
        if (Session::get('role') === ROLE_TEACHER) {
            return true;
        }
        else {
            $this->error('身份验证失败！','index/Index/index');
        }
    }

    /**
     * Method index
     * @purpose 教师主页
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index() {
        $this->roleCheck();
        //统计所有未答和已答课程
        $id = Session::get('id');
        $courses = Course::where('tea_id','=',$id)->select();
        $sum = 0;
        $sum1 = 0; //总数
        foreach($courses as $key=>$course){
            $map1['course_id'] = $course->id;
            $map1['status'] = 0;
            $sum += Question::where($map1)->select()->count();
            $sum1 += Question::where('course_id','=',$course->id)->select()->count();
        }
        $replys = Reply::where('use_id','=',$id)->count(); //回复数量
        $viewData=[
            'unsolved_course' => $sum,
            'solved_course' => ($sum1-$sum),
            'replys' => $replys,
            'courses' => $course->count()
        ];
        $this->assign($viewData);
        return $this->fetch();
    }

    /**
     * Method courseList
     * @purpose 查看课程列表
     * @return \think\response\View
     */
    public function courseList(){
        $this->roleCheck();
        return view();
    }

    /**
     * Method courseTable
     * @purpose 查看课程列表方法
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function courseTable(){
        $this->roleCheck();
        $page = $this->request->param('page');
        $num_per_page = $this->request->param('limit');
        $tea_id=session('id');
        $courses_count = Course::where('tea_id','=',$tea_id)->select()->count();
        $courses = Course::where('tea_id','=',$tea_id)->limit($num_per_page)->page($page)->select();
        $data  = [];
        foreach ($courses as $key => $course) {
            $data[$key]['id'] = $course->id;
            $teacher = User::get($tea_id);
            $data[$key]['tea_name'] = $teacher->name;
            $data[$key]['name'] = $course->name;
            $data[$key]['semester'] = $course->semester;
            $data[$key]['year'] = $course->year;
        }
        // 适应layui接口
        $result = ["code" => 0, "msg" => "成功", "count" => $courses_count, "data" => $data];
        return json($result);
    }

    /**
     * Method questionList
     * @purpose 查看问题列表
     * @return \think\response\View
     */
    public function questionList(){
        $this->roleCheck();
        $cou_id = $this->request->param('course_id');
        $viewdata=[
            'cou_id' =>$cou_id
        ];
        $this->assign($viewdata);
        return view();

    }

    /**
     * Method questionTable
     * @purpose 查看问题方法
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function questionTable(){
        $this->roleCheck();
        $id = input('course_id');
        $map['course_id']=$id;
        $page = $this->request->param('page');
        $num_per_page = $this->request->param('limit');
        $questions_count = Question::where($map)->select()->count();
        $questions = Question::where($map)->limit($num_per_page)->page($page)->select();
        $data  = [];
        foreach ($questions as $key => $question) {
            $data[$key]['id']=$question->id;
            $data[$key]['title']=$question->title;
            if($question->status==0){
                $data[$key]['status']="未解决";
            }
            else{
                $data[$key]['status']="已解决";
            }
        }
        $result = ["code" => 0, "msg" => "成功", "count" => $questions_count, "data" => $data];
        return json($result);
    }

    /**
     * Method courseAdd
     * @purpose 转到添加课程的用户的页面
     * @params role
     */
    public function courseAdd(){
        $this->roleCheck();
        return $this->fetch();
    }
	
    /**
     * Method doUserADD
     * @purpose 添加课程
     * @params id role name password profile email
     */
    public function doCourseAdd(){
        $this->roleCheck();
        $data = [
            'id' => input('id'),
            'tea_id' => input('tea_id'),
            'name' => input('name'),
            'semester' => input('semester'),
            'year' =>input('year'),
        ];
        //判断id是否重复，教师是否存在
        $teachers = User::where('role','=','1');
        $judge = $teachers->where('id','=',$data['tea_id'])->count();
        $judge2 = Course::where('id','=',$data['id'])->count();
        if($judge == 1 && $judge2 == 0){
            $course = Course::create([
                'id' => $data['id'],
                'tea_id' => $data['tea_id'],
                'name' => $data['name'],
                'semester' => $data['semester'],
                'year' => $data['year'],
                'status' => '1'
            ]);
            $result = ['code' => SUCCESS, 'msg' => "新建课程成功"];
        }
        else if($judge2 != 0){
            $result = ['code' => FAILURE, 'msg' => "课程编号重复"];
        }
        else if($judge == 0){
            $result = ['code' => FAILURE, 'msg' => "教师不存在"];
        }
        else{
            $result = ['code' => FAILURE, 'msg' => "添加失败，请稍后再试"];
        }
        return json($result);
    }

    /**
     * Method studentList
     * @purpose 该课程学生列表
     * @return \think\response\View
     */
    public function studentList(){
        $this->roleCheck();
        $cou_id = $this->request->param('course_id');
        $viewdata=[
            'cou_id' =>$cou_id
        ];
        $this->assign($viewdata);
        return view();
    }

    /**
     * Method studentTable
     * @purpose 查看该课程学生
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function studentTable(){
        $this->roleCheck();
        $cou_id = input('cou_id');
        $map['cou_id'] = $cou_id;
        $page = $this->request->param('page');
        $num_per_page = $this->request->param('limit');
        $students_count = Take::where($map)->select()->count();
        $students = Take::where($map)->limit($num_per_page)->page($page)->select();
        $data  = [];
        foreach ($students as $key => $take) {

            $data[$key]['id'] = $take->stu_id;
            $user_id = $take->stu_id;
            $user = User::get($user_id);
            $data[$key]['name'] = $user->name;
            $data[$key]['cou_id'] = $cou_id;
        }
        $result = ["code" => 0, "msg" => "成功", "count" => $students_count, "data" => $data];
        return json($result);
    }

    /**
     * Method studentDelete
     * @purpose 删除课程下的学生
     * @return \think\response\Json
     * @throws \think\Exception
     * @throws \think\exception\PDOException
     */
    public function studentDelete(){
        $this->roleCheck();
        $student_id = $this->request->param('stu_id');
        $course_id = $this->request->param('cou_id');
        $map['stu_id'] = $student_id;
        $map['cou_id'] = $course_id;
        $student = Take::where($map)->delete();;
        $result = ["code" => SUCCESS, "msg" => "成功删除!"];
        return json($result);

    }

    /**
     * Method userAdd
     * @purpose 转到添加该课程学生的页面
     * @params role
     */
    public function studentAdd(){
        $this->roleCheck();
        $cou_id = $this->request->param('course_id');
        $viewData = [
            'cou_id' => $cou_id
        ];
        $this->assign($viewData);
        return $this->fetch();
    }

    /**
     * Method doStudentADD
     * @purpose 添加该课程下学生
     * @params id role name password profile email
     */
    public function doStudentAdd(){
        $this->roleCheck();
        $data = [
            'stu_id' => input('stu_id'),
            'cou_id' => input('cou_id'),
        ];
        $students = User::where('role','=','0');
        $judge = $students->where('id','=',$data['stu_id'])->count();
        $judge2 = Take::where($data)->count();
        if($judge == 1 && $judge2 == 0){
            $take = Take::create([
                'stu_id' => $data['stu_id'],
                'cou_id' => $data['cou_id'],
            ]);
            $result = ['code' => SUCCESS, 'msg' => "添加学生成功" ];
        }
        else if($judge == 0){
            $result = ['code' => FAILURE, 'msg' => "该学生编号不存在" ];
        }
        else if($judge2 == 1){
            $result = ['code' => FAILURE, 'msg' => "该学生已经在此课程中"];
        }
        else{
            $result = ['code' => FAILURE, 'msg' => "用户添加失败，请待会儿重试"];
        }
        return json($result);
    }

}