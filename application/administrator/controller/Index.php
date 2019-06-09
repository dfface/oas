<?php

namespace app\administrator\controller;

use app\common\model\File;
use think\Db;
use app\common\model\Course;
use app\common\model\Question;
use think\Controller;
use think\facade\Env;
use think\facade\Session;
use app\common\model\User;
use app\common\model\Take;
use app\common\model\Reply;
use app\common\model\Carousel;
use PHPExcel_IOFactory;
use PHPExcel;
class Index extends Controller
{
    /**
     * Method roleCheck
     * @purpose 管理员用户身份检查
     * @return integer
    */
    public function roleCheck()
    {
        if(Session::get('role') === ROLE_ADMINISTRATOR ){
            return ROLE_ADMINISTRATOR;
        }elseif(Session::get('role') === ROLE_SUPER_ADMINISTRATOR){
            return ROLE_SUPER_ADMINISTRATOR;
        }else{
            $this->error('身份验证失败！','index/Index/index');
        }
    }

    /**
     * Method index
     * @purpose 管理员主页
     * @return mixed
    */
    public function index(){
        $this->roleCheck();
        $student_count = User::where("role = 0 ")->select()->count();
        $teacher_count = User::where('role = 1')->select()->count();
        $admin_count  = User::where('role = 2 or role = 3')->select()->count();
        $question_count = Question::where('1=1')->select()->count();
        $reply_count = Reply::where('1=1')->select()->count();
        $viewData=[
            'student_count' => $student_count,
            'teacher_count' => $teacher_count,
            'admin_count' => $admin_count,
            'question_count' => $question_count,
            'reply_count' => $reply_count
        ];
        $this->assign($viewData);
        return $this->fetch();
    }

    /**
     * Method userList
     * @purpose 显示用户列表
    */
    public function userList(){
        $this->roleCheck();
        return $this->fetch();
    }
    /**
     * Method userTable
     * @params page,limit
     * @purpose 显示用户列表
     */
    public function userTable(){
        $this->roleCheck();
        $role = input('role');
        $page = $this->request->param('page');
        $num_per_page = $this->request->param('limit') ;
        $users_count = User::where('role',$role)->select()->count();
        $users = User::where('role',$role)->limit($num_per_page)->page($page)->select();
        $data = [];
        foreach ($users as $key => $user){
            $data[$key]['id']           = $user->id;
            $data[$key]['name']         = $user->name;
            $data[$key]['email']        = $user->email;
            $data[$key]['profile']      = $user->profile;
            $data[$key]['role']         = $user->role;
            $data[$key]['create_time']  = $user->create_time;
            $data[$key]['update_time']  = $user->update_time;
        }
        //适应layui接口
        $result = ["code" => 0,"msg" =>"成功","count" => $users_count, "data" => $data];
        return json($result);
    }
    /**
     * Method userDelete
     * @purpose 管理员对用户的删除接口
     * @params id
     * @return \think\response\Json
     */
    public function userDelete() {
        $this->roleCheck();
        $user_id = $this->request->param('id');
        // 找到学生用户
        $student = User::get($user_id);
        $student->delete();
        $result = ["code" => SUCCESS, "msg" => "成功删除!"];
        return json($result);
    }
    /**
     * Method userEdit
     * @params id
     * @purpose 显示用户编辑页面
     */
    public function userEdit(){
        $this->roleCheck();
        $user = User::get($this->request->param('id'));
        $viewData=[
            'user' => $user
        ];
        $this->assign($viewData);
        return $this->fetch();
    }
    /**
     * Method userAdd
     * @params role
     * @prupose 转到添加角色的用户的页面
    */
    public function userAdd(){
        $this->roleCheck();
        $role = $this->request->param('role');
        $viewData = [
            'role' => $role
        ];
        $this->assign($viewData);
        return $this->fetch();
    }
    /**
     * Method doUserADD
     * @params id role name password profile email
    */
    public function doUserAdd(){
        $this->roleCheck();
        $data = [
            'role' => input('role')."",
            'name' => input('name')."",
            'password' => input('password'),
            'id' => input('id')."",
            'email' =>input('email')."",
            'profile' =>input('profile').""
        ];
        if(strlen($data['role'])==0  ){
            $result = ['code' => 1, 'msg' => 'role'];
            return json($result);
        }elseif (strlen($data['name'])== 0)
        {
            $result = ['code' => 1, 'msg' => 'name'];
            return json($result);
        }elseif (strlen($data['password'])== 0)
        {
            $result = ['code' => 1, 'msg' => 'password'];
            return json($result);
        }elseif (strlen($data['profile'])== 0)
        {
            $result = ['code' => 1, 'msg' => 'profile'];
            return json($result);
        }elseif (strlen($data['email'])== 0)
        {
            $result = ['code' => 1, 'msg' => 'email'];
            return json($result);
        }else if(!strstr($data['email'],"@")){
            $result = ['code' => 1, 'msg' => 'email'];
            return json($result);
        }

        $unique = User::get($data['id']);
        if($unique != null){
            $result = ['code' => FAILURE, 'msg' => "该编号的用户已存在"];
            return json($result);
        }
        $user = User::create([
            'id' => $data['id']."",
            'name' => $data['name']."",
            'password' => password_hash($data['password'],PASSWORD_DEFAULT),
            'role' => $data['role']."",
            'email' => $data['email']."",
            'avatar' => '/images/logo.png',
            'profile' => $data['profile'].""
        ]);
        if($user != null){
            $result = ['code' => SUCCESS, 'msg' => "新建用户成功", ];
        }
        else{
            $result = ['code' => FAILURE, 'msg' => "用户创建失败，请待会儿重试"];
        }
        return json($result);
    }
    /**
     * Method batchAdd
     * @purpose 文件上传写入数据库
     * @return \think\response\Json
     */
    public function batchAdd() {
        //上传excel文件
        $this->roleCheck();
        $file = $this->request->file('excel');
        $type = input('type');
        //将文件保存到public/uploads目录下面
        if($file === NULL){
            $result = ['code'=>FAILURE,'msg' =>'文件接收失败','data'=>""];
            return json($result);
        }
        $info = $file->validate(['size'=>1048576,'ext'=>'xls,xlsx'])->move( './uploads');
        if($info){
            //获取上传到后台的文件名
            $fileName = $info->getSaveName();
            if($fileName===""){
                $result = ['code' => FAILURE,'msg'=>'文件名解析错误'];
            }
            //获取文件路径
            $filePath = Env::get('root_path').'public'.DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.$fileName;
            if($filePath==""){
                $result = ['code' => SUCCESS,'msg'=>$filePath];
                return json($result);
            }
            //获取文件后缀
            $suffix = $info->getExtension();
            //判断哪种类型
            if($suffix=="xlsx"){
                $reader = \PHPExcel_IOFactory::createReader('Excel2007');
                if($reader==null){
                    $result = ['code'=> FAILURE,'msg'=>'excel阅读器创建错误'];
                    return json($result);
                }
            }elseif($suffix=="xls"){
                $reader = PHPExcel_IOFactory::createReader('Excel5');
            }else{
                $result = ['code'=>FAILURE,'msg'=>'请上传excel表'];
                return json($result);
            }
        }else{
            $result = ['code'=>FAILURE,'msg' =>'文件过大或格式不正确导致上传失败-_-!','data'=>""];
            return json($result);
        }
        //载入excel文件

        $excel = $reader->load("$filePath",$encode = 'utf-8');
        //读取第一张表
        $sheet = $excel->getSheet(0);
        //获取总行数
        $row_num = $sheet->getHighestRow();
        //获取总列数
        $col_num = $sheet->getHighestColumn();
        $data = []; //数组形式获取表格数据

        if($type == 'user'){
            for ($i = 1; $i <= $row_num; $i ++) {
                $data[$i]['id']  = $sheet->getCell("A".$i)->getValue();
                $data[$i]['name'] = $sheet->getCell("B".$i)->getValue();
                $data[$i]['password'] =  password_hash($sheet->getCell("C".$i)->getValue(),PASSWORD_DEFAULT);
                $data[$i]['role'] = $sheet->getCell("D".$i)->getValue();
                $data[$i]['email'] = $sheet->getCell("E".$i)->getValue();
                $data[$i]['avatar'] = $sheet->getCell("F".$i)->getValue();
                $data[$i]['profile'] = $sheet->getCell("G".$i)->getValue();
                //将数据保存到数据库
            }
            $res = Db::name('user')->strict(false)->insertAll($data);
            //if($this->error())
            $result = ['code'=>SUCCESS,'msg' =>'导入成功',"data"=>""];
            return json($result);

        }elseif($type == 'take'){
            for ($i = 1; $i <= $row_num; $i ++) {
                $data[$i]['stu_id']  = $sheet->getCell("A".$i)->getValue();
                $data[$i]['cou_id'] = $sheet->getCell("B".$i)->getValue();
            }
            $res = Db::name('take')->strict(false)->insertAll($data);
            //if($this->error())
            $result = ['code'=>SUCCESS,'msg' =>'导入成功',"data"=>""];
            return json($result);
        }elseif($type == 'course'){
            for ($i = 1; $i <= $row_num; $i ++) {
                $data[$i]['id']  = $sheet->getCell("A".$i)->getValue();
                $data[$i]['tea_id'] = $sheet->getCell("B".$i)->getValue();
                $data[$i]['name'] =  password_hash($sheet->getCell("C".$i)->getValue(),PASSWORD_DEFAULT);
                $data[$i]['semester'] = $sheet->getCell("D".$i)->getValue();
                $data[$i]['year'] = $sheet->getCell("E".$i)->getValue();
                $data[$i]['status'] = $sheet->getCell("F".$i)->getValue();
                //将数据保存到数据库
            }
            $res = Db::name('course')->strict(false)->insertAll($data);
            //if($this->error())
            $result = ['code'=>SUCCESS,'msg' =>'导入成功',"data"=>""];
            return json($result);

        }

    }
    /**
     * Method doUserEdit
     * @purpose 修改用户信息
     */
    public function doUserEdit(){
        $this->roleCheck();
        //获取要修改的信息，其中除密码外所有信息若不修改都由旧信息顶替
        $id = $this->request->param('id');
        //获得用户
        $user = User::get($id);
        $name = $this->request->param('name');
        $password = $this->request->param('password');
        $password_confirm = $this->request->param('password_confirm');
        $email = $this->request->param('email');
        $profile = $this->request->param('profile');
        if($email === "" and $password === "" and $name === "" and $profile === "" and $password_confirm === ""){
            $result= ["code" => FAILURE, "msg" => "请输入要修改的信息！"];
            return json($result);
        }
        else{
            if($password_confirm == $password ){
                if($password != ""){
                    $user->password = password_hash($password_confirm,PASSWORD_DEFAULT) ;//语句被修改
                }else {
                    //不修改密码
                }
            }
            else {
                $result= ["code" => FAILURE, "msg" => "两次输入的密码不一致"];
                return json($result);
            }
            if ($email === "") {
                // 没有更新邮箱
            }
            elseif(!strtr($email,"@")){
                $result= ["code" => FAILURE, "msg" => "email中缺少@符号"];
                return json($result);
            }else{
                $user->email = $email;
            }
            if ($name === "") {
                // 没有更新姓名
            }
            else {
                $user->name = $name;
            }
            if ($profile === "") {
                // 没有更新个人简介
            }
            else {
                $user->profile = $profile;
            }
            // 整体更改，只更新变化的数据
            if ($user->save()) {
                // 成功更新字段 百分之九十九成功
            }
            else {
                $result = ["code" => FAILURE, "msg" => "数据库错误，资料修改失败！"];
                return json($result);
            }
            $result = ["code" => SUCCESS, "msg" => "修改成功"];
        }
        return json($result);
    }

    /**
     * Method avatarUpload
     * @purpose 头像上传写入数据库和 session 并返回
     * @return \think\response\Json
     */
    public function avatarUpload() {
        $this->roleCheck();
        $id = input('id');
        $file = $this->request->file('avatar');//报错
        $file_name = $file->getFilename();
        $file_size = $file->getSize();
        $info = $file->move('./images');
        if ($info) {
            // 注意图片的地址
            $path = 'http://oas.com/images/'.$info->getSaveName();
            // 存入数据库 修改 session
            $user = User::get($id);
            $user->avatar = $path;
            $user->save();
            // 写入文件表数据库
            $file_db = new File();
            $file_db->use_id = $id;
            $file_db->name = $file_name;
            $file_db->url = $path;
            $file_db->desc = date('Y年m月d日')." 修改资料时上传的头像";
            $file_db->size = $file_size;
            $file_db->save();
            // 传回消息
            $result = ["code" => SUCCESS, "msg" => "成功", "data" => $path];
        }
        else {
            $result = ["code" => FAILURE, "msg" => $info->getError()];
        }
        return json($result);
    }

    /**
     * Method courseList
     * @purpose 显示课程列表
     */
    public function courseList(){
        $this->roleCheck();
        return $this->fetch();
    }

    /**
     * Method courseTable
     * @purpose 返回课程列表信息
    */
    public function courseTable(){
        $this->roleCheck();
        $page = $this->request->param('page');
        $num_per_page = $this->request->param('limit') ;
        $courses_count = Course::where('1','=', '1')->select()->count();
        $courses = Course::where('1','=', '1')->limit($num_per_page)->page($page)->select();
        $data = [];
        foreach ($courses as $key => $course){
            $data[$key]['id']           = $course->id;
            $data[$key]['tea_id']       = $course->tea_id;
            $data[$key]['name']         = $course->name;
            $data[$key]['semester']     = $course->semester;
            $data[$key]['year']         = $course->year;
            $data[$key]['status']       = $course->status;
            $data[$key]['create_time']  = $course->create_time;
            $data[$key]['update_time']  = $course->update_time;
            $data[$key]['delete_time']  = $course->delete_time;
        }
        //适应layui接口
        $result = ["code" => 0,"msg" =>"成功","count" => $courses_count, "data" => $data];
        return json($result);
    }

    /**
     * @Method courseAdd
     * @purpose 添加课程页面
     */
    public function courseAdd(){
        $this->roleCheck();
        return $this->fetch();
    }

    /**
     * @Method doCourseAdd
     * @purpose 添加课程方法
     */
    public function doCourseAdd(){
        $this->roleCheck();
        $data=[
            'id'=>input('id')."",
            'tea_id'=> input('tea_id')."",
            'name' => input('name')."",
            'semester'=> input('semester')."",
            'year' => input('year').""
        ];
        if(strlen($data['id'])==0){

            $result = ['code' => FAILURE,'msg'=> '课程编号不能为空'];
            return json($result);
        }else{
            $course = Course::get($data['id']);
            if($course !=  null){
                $result = ['code' => FAILURE,'msg'=> '该课程编号已存在'];
                return json($result);
            }
        }
        if(strlen($data['tea_id'])==0){
            $result = ['code' => FAILURE,'msg'=> '授课教师编号不能为空'];
            return json($result);
        }
        else{
            $where=[
              ['role' ,'=', '1'],
              ['id','=',$data['tea_id']]
            ];
            $teachers_count = User::where($where)->select()->count();
            if($teachers_count === 0)
            {
                $result = ['code' => FAILURE,'msg'=> '无此教师'];
                return json($result);
            }
        }
        if(strlen($data['name'])==0){
            $result = ['code' => FAILURE,'msg'=> '课程名称不能为空'];
            return json($result);
        }else{
            $year = (int)($data['year']);
            if($year>2070) {
                $result = ['code' => FAILURE,'msg'=> '年份过大'];
                return json($result);
            }
            if($year < 2000) {
                $result = ['code' => FAILURE,'msg'=> '年份过小'];
                return json($result);
            }
        }
        $course = Course::create([
            'id'=>$data['id'],
            'tea_id'=> $data['tea_id'],
            'name' => $data['name'],
            'semester'=> $data['semester'],
            'year' => $data['year']
        ]);
        if($course!=null){
            $result = ['code' => SUCCESS,'msg'=> '新建课程成功'];
        }
        else{
            $result = ['code' => FAILURE,'msg'=>'数据库新建课程错误，创建了空课程，请重试'];
        }
        return json($result);
    }

    /**
     * @Method courseEdit
     * @params field value id
     * @purpose 更新课程信息
     */
    public function courseEdit(){
        $this->roleCheck();
        $data=[
            'id' => input('id'),
            'field'=> input('field'),
            'value'=>input('value')
        ];
        if($data['field'] == 'tea_id' ){
            $where = [
                ['id','=',"".$data['value']],
                ['role','=','1']
            ];
            $result = User::where($where)->select()->count();
            if($result === 0){
                $msg = ['code' => FAILURE, 'msg' => "没有id为".$data['value']."的教师"];
                return json($msg);
            }
        }
        $result =  Db::table('course')->where('id', $data['id'])->update([$data['field'] => $data['value'] ]);
        if($result >0 ){
            $msg = ["code" => SUCCESS, "msg" => "成功修改!"];
            return json($msg);
        }
        else{
            $msg = ["code" => FAILURE, "msg" => "修改失败或未作修改!"];
            return json($msg);
        }
    }

    /**
     * Method courseDelete
     * @purpose 管理员对课程的删除接口
     * @params id
     * @return \think\response\Json
     */
    public function courseDelete() {
        $this->roleCheck();
        $course_id = $this->request->param('id');
        // 找到课程
        $course = Course::get($course_id);
        if($course== null){
            $result = ["code" => FAILURE, "msg" => "没有这门课!"];
            return json($result);

        }
        $course->delete();
        $result = ["code" => SUCCESS, "msg" => "成功删除!"];
        return json($result);
    }

    /**
     * Method takeTable
     * @params page,limit
     * @purpose 显示选课信息
    */
    public function takeTable(){
        $this->roleCheck();
        $page = $this->request->param('page');
        $num_per_page = $this->request->param('limit') ;
        $takes_count = Take::where('1', '=','1')->select()->count();
        $takes = Take::where('1', '=','1')->limit($num_per_page)->page($page)->select();
        $data = [];
        foreach ($takes as $key => $take){
            $data[$key]['stu_id']           = $take->stu_id;
            $data[$key]['cou_id']       = $take->cou_id;
        }
        //适应layui接口
        $result = ["code" => 0,"msg" =>"成功","count" => $takes_count, "data" => $data];
        return json($result);
    }

    /**
     * @Method takeEdit
     * @params field value id
     * @purpose 更新选课信息
     */
    public function takeEdit(){
        $this->roleCheck();
        $data=[
            'cou_id' => input('cou_id'),
            'stu_id' => input('stu_id'),
            'field'=> input('field'),
            'value'=>input('value')
        ];
        if($data['value'] =="")
            return json(["code" => FAILURE, "msg" => "传入参数为空!"]);
        $result = Take::get(['stu_id'=> "".$data['stu_id'],'cou_id'=>"".$data['cou_id']]);
        if($data['field'] == 'cou_id'){
            //判断要修改的选课记录是否已经存在与数据库中
            $alreadyHas = Take::get(['stu_id'=> "".$data['stu_id'],'cou_id'=>"".$data['value']]);
            if($alreadyHas != null){
                return json(['code'=>FAILURE,'msg'=>'已经存在该条选课记录']);
            }
            $where = [
                ['id','=',"".$data['value']],
            ];
            $count = Course::where($where)->select()->count();
            if($count == 1){
                Take::create([
                    'stu_id'=>$data['stu_id'],
                    'cou_id'=>$data['value']
                ]);
                $result->delete();
                $msg=['code'=> SUCCESS,'msg'=>'修改选课课程成功'];
                return json($msg);
            }else{
                $msg=['code'=> FAILURE,'msg'=>'不存在这个课程id'];
                return json($msg);
            }
        }elseif($data['field'] == 'stu_id'){
            //判断要修改的选课记录是否已经存在与数据库中
            $alreadyHas = Take::get(['stu_id'=> "".$data['value'],'cou_id'=>"".$data['cou_id']]);
            if($alreadyHas != null){
                return json(['code'=>FAILURE,'msg'=>'已经存在该条选课记录']);
            }
            $where = [
                ['id','=',"".$data['value']],
                ['role','=','0']
            ];
            $count = User::where($where)->select()->count();
            if($count == 1){
                Take::create([
                    'stu_id'=>$data['value'],
                    'cou_id'=>$data['cou_id']
                ]);
                $result->delete();
                $msg=['code'=> SUCCESS,'msg'=>'修改选课学生id成功'];
                return json($msg);
            }else{
                $msg=['code'=> FAILURE,'msg'=>'不存在这个学生id'];
                return json($msg);
            }
        }
    }

    /**
     * Method takeDelete
     * @purpose 管理员对选课信息的删除接口
     * @params id
     * @return \think\response\Json
     */
    public function takeDelete() {
        $this->roleCheck();
        $stu_id = $this->request->param('stu_id');
        $cou_id = $this->request->param('cou_id');
        $result = Take::get(['stu_id'=> "".$stu_id,'cou_id'=>"".$cou_id])->delete();
        if($result != 0){
            $result = ["code" => SUCCESS, "msg" => "成功删除"];
            return json($result);
        }else {
            $result = ["code" => FAILURE, "msg" => "删除失败"];
            return json($result);

        }
    }

    /**
     * @Method takeAdd
     * @purpose 显示添加选课记录页面
    */
    public function takeAdd(){
        $this->roleCheck();
        return $this->fetch();
    }

    /**
     * @Method doTakeAdd
     * @purpose 添加选课记录的方法
     * @params cou_id stu_id
    */
    public function doTakeAdd(){
        $this->roleCheck();
        $data=[
            'cou_id' => input('cou_id')."",
            'stu_id' => input('stu_id').""
        ];
        if(strlen($data['cou_id']) == 0 ){
            $result = ['code' => FAILURE,'msg' => "课程编号不能为空"];
            return json($result);
        }
        $where_cou = [['id' , '=', $data['cou_id'].""]];
        $where_stu = [
            ['id', '=', "".$data['stu_id']],
            ['role','=','0']
        ];
        $course_count = Course::where($where_cou)->select()->count();
        $student_count = User::where($where_stu)->select()->count();
        if($course_count == 0){
            $result = ['code'=>FAILURE,'msg' => '无此课程'];
            return json($result);
        }
        if($student_count == 0){
            $result = ['code' => FAILURE,'msg'=>'无此学生'];
            return json($result);
        }
        $result = Take::get(['stu_id'=> "".$data['stu_id'],'cou_id'=>"".$data['cou_id']]);
        if($result != null){
            $result = ['code' => FAILURE, 'msg' => '学生已经选中次课，无需再选'];
        }
        $take = Take::create([
            'cou_id' => $data['cou_id'],
            'stu_id' => $data['stu_id']
        ]);
        $result = ['code'=> SUCCESS,'msg' => '创建选课记录成功'];
        return json($result);
    }

    /**
     * Method contentManage
     *
    */
    public function contentManage(){
        $this->roleCheck();
        return $this->fetch();
    }

    /**
     * Method questionTable
     * @params page,limit
     * @purpose 显示问题列表
     */
    public function questionTable(){
        $this->roleCheck();
        $page = $this->request->param('page');
        $num_per_page = $this->request->param('limit') ;
        $questions_count = question::where('title' ,'like','%%')->select()->count();
        $questions = question::where('title' ,'like','%%')->limit($num_per_page)->page($page)->select();
        $data = [];
        foreach ($questions as $key => $question){
            $data[$key]['id']           = $question->id;
            $data[$key]['stu_id']         = $question->stu_id;
            $data[$key]['course_id']        = $question->course_id;
            $data[$key]['title']      = $question->title;
            $data[$key]['content']         = $question->content;
            $data[$key]['status']=$question->status;
            $data[$key]['create_time']  = $question->create_time;
            $data[$key]['update_time']  = $question->update_time;
        }
        //适应layui接口
        $result = ["code" => 0,"msg" =>"成功","count" => $questions_count, "data" => $data];
        return json($result);
    }

    /**
     * Method questionDelete
     * @purpose 管理员对问题的删除接口
     * @params id
     * @return \think\response\Json
     */
    public function questionDelete() {
        $this->roleCheck();
        $question_id = $this->request->param('id');
        // 找到学生问题
        $question = question::get($question_id);
        $question->delete();
        $result = ["code" => SUCCESS, "msg" => "成功删除!"];
        return json($result);
    }

    /**
     * @Method questionEdit
     * @params
     * @purpose 修改问题信息
     */
    public function questionEdit(){
        $this->roleCheck();
        $data=[
            'id' => input('id'),
            'field'=> input('field'),
            'value'=>input('value')
        ];
        $result =  Db::table('question')->where('id', $data['id'])->update([$data['field'] => $data['value'] ]);
        if($result != 0){
            $msg = ["code" => SUCCESS, "msg" => "成功修改!"];
            return json($msg);
        }
        else{
            $msg = ["code" => FAILURE, "msg" => "修改失败或未作修改!"];
            return json($msg);
        }
    }

    /**
     * Method questionContentEdit
     * @purpose 展示问题标题和内容的编辑页面
     */
    public function questionContentEdit(){
        $this->roleCheck();
        $data['id']=[
            'id' => input('id')
        ];
        $question = Question::get($data['id']);
        $viewData=[
            'question' => $question
        ];
        $this->assign($viewData);
        return $this->fetch();
    }

    /**
     * Method doQuestionContentEdit
     */
    public function doQuestionContentEdit(){
        $this->roleCheck();
        $data = [
            'id' => input('id'),
            'title' => input('title'),
            'content'=> input('content')
        ];
        $question = Question::get($data['id']);
        $question->title = $data['title'];
        $question->content = $data['content'];
        $question->save();
        $result = ['code'=> SUCCESS,'msg'=> '修改成功！'];
        return json($result);
    }

    /**
     * Method replyDelete
     * @purpose 管理员对回复的删除接口
     * @params id
     * @return \think\response\Json
     */
    public function replyDelete() {
        $this->roleCheck();
        $reply_id = $this->request->param('id');
        // 找到回复
        $reply = reply::get($reply_id);
        $reply->delete();
        $result = ["code" => SUCCESS, "msg" => "成功删除!"];
        return json($result);
    }

    /**
     * @Method replyEdit
     * @params
     * @purpose 修改回复信息
     */
    public function replyEdit(){
        $this->roleCheck();
        $data=[
            'id' => input('id'),
            'field'=> input('field'),
            'value'=>input('value')
        ];
        $result =  Db::table('reply')->where('id', $data['id'])->update([$data['field'] => $data['value'] ]);
        if($result != 0){
            $msg = ["code" => SUCCESS, "msg" => "成功修改!"];
            return json($msg);
        }
        else{
            $msg = ["code" => FAILURE, "msg" => "修改失败或未作修改!"];
            return json($msg);
        }
    }

    /**
     * Method replyContentEdit
     * @purpose 展示问题标题和内容的编辑页面
     */
    public function replyContentEdit(){
        $this->roleCheck();
        $data['id']=[
            'id' => input('id')
        ];
        $reply = Reply::get($data['id']);
        $viewData=[
            'reply' => $reply
        ];
        $this->assign($viewData);
        return $this->fetch();
    }

    /**
     * Method doReplyContentEdit
     */
    public function doReplyContentEdit(){
        $this->roleCheck();
        $data = [
            'id' => input('id'),
            'content'=> input('content')
        ];
        $reply = Reply::get($data['id']);
        $reply->content = $data['content'];
        $reply->save();
        $result = ['code'=> SUCCESS,'msg'=> '修改成功！'];
        return json($result);
    }

    /**
     * Method replyTable
     * @params page,limit
     * @purpose 显示回复列表
     */
    public function replyTable(){
        $this->roleCheck();
        $role = input('role');
        $page = $this->request->param('page');
        $num_per_page = $this->request->param('limit') ;
        $replies_count = reply::where('content' ,'like','%%')->select()->count();
        $replies = reply::where('content' ,'like','%%')->limit($num_per_page)->page($page)->select();
        $data = [];
        foreach ($replies as $key => $reply){
            $data[$key]['id']           = $reply->id;
            $data[$key]['que_id']         = $reply->que_id;
            $data[$key]['use_id']        = $reply->use_id;
            $data[$key]['content']      = $reply->content;
            $data[$key]['status']         = $reply->status;
            $data[$key]['like_count']=$reply->like_count;
            $data[$key]['dislike_count']  = $reply->dislike_count;
            $data[$key]['is_second_reply']  = $reply->is_second_reply;
        }
        //适应layui接口
        $result = ["code" => 0,"msg" =>"成功","count" => $replies_count, "data" => $data];
        return json($result);
    }

    /**
     * @Method carouselTable
     * @purpose 轮播图列表
    */
    public function carouselTable(){
        $this->roleCheck();
        $page = $this->request->param('page');
        $num_per_page = $this->request->param('limit') ;
        $where = "1=1";
        $carousels_count = Carousel::where($where)->select()->count();
        $carousels = Carousel::where($where)->limit($num_per_page)->page($page)->select();
        $data=[];
        foreach($carousels as $key => $carousel){
            $data[$key]['id'] = $carousel['id'];
            $data[$key]['imageUrl'] = $carousel['url'];
        }
        $result = ['code' => 0,"msg" => "成功", "count" => $carousels_count, "data" => $data];
        return json($result);
    }

    /**
     * @Method carouselAdd
     * @purpose 添加轮播图页面
    */
    public function carouselAdd(){
        $this->roleCheck();
        return $this->fetch();
    }

    /**
     * @Method doCarouselAdd
     * @purpose 添加备选轮播图到数据库
    */
    public function  doCarouselAdd(){
        $this->roleCheck();
        $data=['url' =>input('url')];
        if(strlen($data['url'])!=0){
            Carousel::create(['url' => $data['url']]);
            $result= ['code' => SUCCESS,'msg'=> '成功'];
        }
        else {
            $result = ["code" => FAILURE, "msg" => "没有收到链接！"];
        }
        return json($result);
    }

    /**
     * Method doCarouselUpload
     * @purpose 轮播图上传
     * @return \think\response\Json
     */
    public function doCarouselUpload() {
        $this->roleCheck();
        $file = $this->request->file('carousel');
        $file_size = $file->getSize();
        $info = $file->move('./images');
        if ($info) {
            // 注意图片的地址
            $path = '/images/'.$info->getSaveName();
            // 存入轮播图数据库
            $carousel = Carousel::create(['url' => $path]);
            // 写入文件表数据库
            $file_db = new File();
            $file_db->use_id = Session::get('id');
            $file_db->name = "轮播图 ".$carousel->id;
            $file_db->url = $path;
            $file_db->desc = date('Y年m月d日')." 上传的轮播图";
            $file_db->size = $file_size;
            $file_db->save();
            $result = ["code" => SUCCESS, "msg" => "成功"];
        }
        else {
            $result = ["code" => FAILURE, "msg" => $info->getError()];
        }
        return json($result);
    }

    /**
     * Method carouselDelete
     * @params id
     */
    public function carouselDelete(){
        $this->roleCheck();
        $data = [
            'id' => input('id')
        ];
        $carousel = reply::get($data['id']);
        $carousel->delete();
        $result = ["code" => SUCCESS, "msg" => "成功删除!"];
        return json($result);
    }

    /**
     * @Method search
     * @params role, table, search_field, search_field_value
     * @purpose 根据要搜索的表、字段字段的模糊值进行搜索，role为搜索用户专用，当不搜索用户时，其值为-1
     */
    public function search(){
        $this->roleCheck();
        $viewData=[
            'role' => input('role'),
            'search_table' => input('table'),
            'search_field' => input('search_field'),
            'search_field_value' => input('search_field_value')
        ];
        $this->assign($viewData);
        return $this->fetch();
    }

    /**
     * Method doSearch
     * @params role, table, search_field, search_field_value
     * @purpose 执行搜索功能
     */
    public function doSearch(){
        $this->roleCheck();
        $viewData=[
            'role' => input('role'),
            'search_table' => input('search_table'),
            'search_field' => input('search_field'),
            'search_field_value' => input('search_field_value')
        ];
        $page = $this->request->param('page');
        $num_per_page = $this->request->param('limit') ;
        $where = [
            [$viewData['search_field'],'like',"%".$viewData['search_field_value']."%"]
        ];
        if($viewData['search_table']==='user'){
            $where = [
                [ 'role', '=',$viewData['role']],
                [$viewData['search_field'],'like',"%".$viewData['search_field_value']."%"]
            ];
            $users_count = User::where($where)->select()->count();
            $users = User::where($where)->limit($num_per_page)->page($page)->select();
            if($users_count === 0 ){
                $result = ['code' => 0, "msg" =>"成功"];
                return json($result);
            }
            $data = [];
            foreach ($users as $key => $user){
                $data[$key]['id']           = $user->id;
                $data[$key]['name']         = $user->name;
                $data[$key]['email']        = $user->email;
                $data[$key]['profile']      = $user->profile;
                $data[$key]['role']         = $user->role;
                $data[$key]['create_time']  = $user->create_time;
                $data[$key]['update_time']  = $user->update_time;
            }
            //适应layui接口
            $result = ['code' => 0 ,"msg" =>"成功","count" => $users_count, "data"=>$data];
            return json($result);
        }
        else if($viewData['search_table']==='course'){
            $courses_count = Course::where($where)->select()->count();
            $courses = Course::where($where)->limit($num_per_page)->page($page)->select();
            if($courses_count === 0 ){
                $result = ['code' => 0, "msg" =>"没有数据"];
                return json($result);
            }
            $data = [];
            foreach ($courses as $key => $course){
                $data[$key]['id']           = $course->id;
                $data[$key]['tea_id']         = $course->tea_id;
                $data[$key]['name']        = $course->name;
                $data[$key]['semester']      = $course->semester;
                $data[$key]['year']         = $course->year;
                $data[$key]['status']  = $course->status;
            }
            //适应layui接口
            $result = ['code' => 0 ,"msg" =>"成功","count" => $courses_count, "data"=>$data];
            return json($result);
        }
        else if($viewData['search_table']==='take'){
            $takes_count = Take::where($where)->select()->count();
            $takes = Take::where($where)->limit($num_per_page)->page($page)->select();
            if($takes_count === 0 ){
                $result = ['code' => 0, "msg" =>"没有数据"];
                return json($result);
            }
            $data = [];
            foreach ($takes as $key => $take){
                $data[$key]['stu_id']           = $take->stu_id;
                $data[$key]['cou_id']         = $take->cou_id;
            }
            //适应layui接口
            $result = ['code' => 0 ,"msg" =>"成功","count" => $takes_count, "data"=>$data];
            return json($result);
        }
        else if($viewData['search_table']==='question'){
            $questions_count = Question::where($where)->select()->count();
            $questions = Question::where($where)->limit($num_per_page)->page($page)->select();
            if($questions_count === 0 ){
                $result = ['code' => 0, "msg" =>"没有数据"];
                return json($result);
            }
            $data = [];
            foreach ($questions as $key => $question){
                $data[$key]['id']           = $question->id;
                $data[$key]['stu_id']         = $question->stu_id;
                $data[$key]['course_id']           = $question->course_id;
                $data[$key]['title']         = $question->title;
                $data[$key]['content']           = $question->content;
                $data[$key]['status']         = $question->status;
            }
            //适应layui接口
            $result = ['code' => 0 ,"msg" =>"成功","count" => $questions_count, "data"=>$data];
            return json($result);
        }
        else if($viewData['search_table']==='reply'){
            $replies_count = Reply::where($where)->select()->count();
            $replies = Reply::where($where)->limit($num_per_page)->page($page)->select();
            if($replies_count === 0 ){
                $result = ['code' => 0, "msg" =>"没有数据"];
                return json($result);
            }
            $data = [];
            foreach ($replies as $key => $reply){
                $data[$key]['id']           = $reply->id;
                $data[$key]['stu_id']         = $reply->stu_id;
                $data[$key]['course_id']           = $reply->course_id;
                $data[$key]['title']         = $reply->title;
                $data[$key]['content']           = $reply->content;
                $data[$key]['status']         = $reply->status;
            }
            //适应layui接口
            $result = ['code' => 0 ,"msg" =>"成功","count" => $replies_count, "data"=>$data];
            return json($result);
        }
    }
}
