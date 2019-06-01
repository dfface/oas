<?php

namespace app\index\controller;

use app\common\model\Course;
use app\common\model\File;
use app\common\model\Question;
use app\common\model\Reply;
use think\Controller;
use think\facade\Session;
use app\common\model\User;
use think\Image;

class Index extends Controller
{
    /**
     * Method idCheck
     * @purpose 通过检查id的存在而判断登录与否
     */
    public function idCheck() {
        // 暂时不需要
    }

    /**
     * Method index
     * @purpose 主页显示
     * @return mixed
     */
    public function index() {
        return $this->fetch();
    }

    /**
     * Method login
     * @purpose 登录表单显示
     * @return mixed
     */
    public function login() {
        return $this->fetch();
    }

    /**
     * Method doLogin
     * @purpose 登录验证
     * @params id password
     * @return \think\response\Json
     */
    public function doLogin()
    {
        // 注意此处获取参数的方法
        $id = $this->request->param('id');
        $password = $this->request->param('password');
        $user = User::get($id);
        if($user){
            $password_in_db = $user->password;
            if(password_verify($password,$password_in_db)){
                if(Session::has('id')) {
                    // 如果已经登录，则发出提示
                }
                else {
                    Session::set('role', $user->role);
                    Session::set('id', $id);
                    Session::set('name',$user->name);
                    Session::set('last_login_time', $user->last_login_time);
                    Session::set('email', $user->email);
                    Session::set('avatar', $user->avatar);
                    Session::set('profile', $user->profile);
                    // 创建导航栏快捷调用个人中心 所需完整的模块名
                    switch($user->role) {
                        case ROLE_STUDENT:
                            Session::set('role_name', 'student');
                            break;
                        case ROLE_TEACHER:
                            Session::set('role_name', 'teacher');
                            break;
                        case ROLE_ADMINISTRATOR:
                            Session::set('role_name', 'administrator');
                            break;
                        case ROLE_SUPER_ADMINISTRATOR:
                            Session::set('role_name', 'administrator');
                            break;
                        default:
                            $result = ["code" => FAILURE, "msg" =>  "角色不正确！"];
                    }
                    // 写入登录时间
                    $user->last_login_time = date('Y-m-d H:i:s');
                    $user->save();
                }
                $result = ["code" => SUCCESS, "msg" =>  "登录成功！"];
            }
            else{
                $result = ["code" => FAILURE, "msg" => "密码错误！"];
            }
        }
        else{
            $result = ["code" => FAILURE, "msg" => "用户名不存在！"];
        }
        return json($result);
    }

    /**
     * Method logout
     * @purpose 登出
     */
    public function logout() {
        if(Session::has('role')) {
            // 清除 cookie
            cookie('PHPSESSID',null);
            // 清除 session
            session(null);
            // 重定向
            $this->redirect('index/Index/index');
        }
        else{
            $this->error('身份验证失败！','index/Index/index');
        }
    }

    /**
     * Method informationModify
     * @purpose 进入信息修改的页面
     * @return mixed
     */
    public function informationModify() {
        if (Session::has('id')) {
            return $this->fetch();
        }
        else {
            $this->error('身份验证失败！','index/Index/index');
        }
    }

    /**
     * Method 修改资料
     * @params id password_origin password_new email_new
     * @return \think\response\Json
     */
    public function doInfoModify() {
        if (Session::has('id')) {
            // 获取用户
            $user = User::get(Session::get('id'));
            // 获取更改值
            $password_origin = $this->request->param('password_origin');
            $password_new = $this->request->param('password');
            $email_new = $this->request->param('email');
            // 取原始值进行对比
            // 如果既没有更改密码又没有更改邮箱
            // 变量作用域
            $result = '' ;
            if ($email_new === "" and $password_new === "") {
                $result = ["code" => FAILURE, "msg" => '请输入要修改的信息！'];
            }
            else {
                if ($password_new === "") {
                    // 没有修改密码
                }
                else {
                    if ($password_origin === "") {
                        // 要修改密码却没有填原始值
                        $result = ["code" => FAILURE, "msg" => '想要修改密码必须填写旧密码！'];
                    }
                    else {
                        if (password_verify($password_origin, $user->password)) {
                            // 验证成功，可以更改密码
                            $user->password = password_hash($password_new, PASSWORD_DEFAULT);
                            $result = ["code" => SUCCESS, "msg" => "成功"];
                        }
                        else {
                            // 验证失败，旧密码错误
                            $result = ["code" => FAILURE, "msg" => "旧密码错误！"];
                        }
                    }
                }
                if ($email_new === "") {
                    // 没有更新邮箱
                }
                else {
                    $user->email = $email_new;
                    // 修改 session
                    Session::set('email',$email_new);
                    $result = ["code" => SUCCESS, "msg" => "成功"];
                }
                // 整体更改，只更新变化的数据
                if ($user->save()) {
                    // 成功更新字段 百分之九十九成功
                }
                else {
                    $result = ["code" => FAILURE, "msg" => "数据库错误，资料修改失败！"];
                }
            }
            return json($result);
        }
        else {
            $this->error('身份验证失败！','index/Index/index');
        }
    }

    /**
     * Method avatarUpload
     * @purpose 头像上传写入数据库和 session 并返回
     * @return \think\response\Json
     */
    public function avatarUpload() {
        if (Session::has('id')) {
            $file = $this->request->file('avatar');
            $file_name = $file->getFilename();
            $file_size = $file->getSize();
            $info = $file->move('./images');
            if ($info) {
                // 注意图片的地址
                $path = 'http://oas.com/images/'.$info->getSaveName();
                // 存入数据库 修改 session
                $user = User::get(Session::get('id'));
                $user->avatar = $path;
                $user->save();
                Session::set('avatar',$path);
                // 写入文件表数据库
                $file_db = new File();
                $file_db->use_id = Session::get('id');
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
        else {
            $this->error('身份验证失败！','index/Index/index');
        }
    }

    /**
     * Method questionInspect
     * @purpose 问题查看入口
     * @params question_id
     */
    public function questionInspect() {
        $question = Question::get($this->request->param('question_id'));
        $question->read_count += 1;
        $question->save();
        // 问题本身属性
        $this->assign('question_id', $question->id);
        $this->assign('title', $question->title);
        $this->assign('content', $question->content);
        $this->assign('read_count', $question->read_count);
        $this->assign('status',  $question->status);
        $this->assign('create_time', $question->create_time);
        // 回复的相关属性
        $replies_count = Reply::where('que_id', $question->id)->count();
        $this->assign('replies_count', $replies_count);
        return $this->fetch();
    }

    /**
     * Method repliesPage
     * @purpose ajax 实现分页
     */
    public function repliesPage() {
        $page = $this->request->param('page');
        $question_id = $this->request->param('question_id');
        $replies = Reply::where('que_id',$question_id)->order('id','desc')->limit(10)->page($page)->select();
        // 强调关联模型作为属性存在
        for ( $i = 0; $i < $replies->count(); $i++) {
            $user = User::get($replies[$i]->use_id);
            $replies[$i]["user_name"] = $user->name;
            $replies[$i]["user_avatar"] = $user->avatar;
            $replies[$i]["user_role"] = $user->role;
        }
        $result = ["code" => SUCCESS, "msg" => "成功", "data" => $replies];
        return json($result);
    }

    /**
     * Method imageUpload
     * @purpose 适配编辑器：图片加水印并上传 接口
     * 返回 success message url
     * success : 0 | 1,           // 0 表示上传失败，1 表示上传成功
     * message : "提示的信息，上传成功或上传失败及错误信息等。",
     * url     : "图片地址"        // 上传成功时才返回
     */
    public function imageUpload() {
        if(Session::get('id')) {
            $file = $this->request->file('editormd-image-file');  // 注意上传的字段
            $file_name = $file->getFilename();
            // 移动文件
            $info = $file->move('./images/'); // 注意保存为 info
            // 开始对图像加水印
            $path_name = './images/'.$info->getSaveName();
            $image = Image::open($path_name);
            $image->water('./images/logo_min.png',\think\Image::WATER_NORTHWEST)->text('oas '.date('Y-m-d h:i:s'),'Alibaba-PuHuiTi-Regular.otf',10)->save($path_name);
            $path = 'http://oas.com/images/'.$info->getSaveName();  // 注意图片的地址
            // 写入文件表
            File::create([
                'use_id' => Session::get('id'),
                'name' => $file_name,
                'url' => $path,
                'desc' => date('Y年m月d日')." 上传的图片",
                'size' => filesize($path_name),
            ]);
            //  传回消息
            if($file){
                $result = ['success' => 1, "message" => "上传成功", 'url' => $path];
            }
            else{
                $result = ['success' => 0, "message" => $file->getError()];
            }
            return json($result);
        }
        else {
            $this->error('身份验证失败！','index/Index/index');
        }
    }

    /**
     * Method like
     * @purpose 点赞处理
     * @params rep_id
     */
    public function like() {
        if ($rep_id = $this->request->param('rep_id')) {
            $reply = Reply::get($rep_id);
            $reply->like_count += 1;
            $reply->save();
            $like_count = $reply->like_count;
            $result = ["code" => SUCCESS, "msg" => "成功", "data" => $like_count];
        }
        else {
            $result = ["code" => FAILURE, "msg" => "没有收到id！"];
        }
        return json($result);
    }

    /**
     * Method dislike
     * @purpose 拍砖处理
     * @params  rep_id
     * @return \think\response\Json
     */
    public function dislike() {
        if ($rep_id = $this->request->param('rep_id')) {
            $reply = Reply::get($rep_id);
            $reply->dislike_count += 1;
            $reply->save();
            $dislike_count = $reply->dislike_count;
            $result = ["code" => SUCCESS, "msg" => "成功", "data" => $dislike_count];
        }
        else {
            $result = ["code" => FAILURE, "msg" => "没有收到id！"];
        }
        return json($result);
    }

    /**
     * Method replySubmit
     * @purpose 回复问题的提交接口
     * @params use_id que_id
     * @return \think\response\Json
     */
    public function replySubmit() {
        if ($user_id = session('id')){
            $que_id = $this->request->param('que_id');
            $content = $this->request->param('content');
            $reply = Reply::create([
                "que_id" => $que_id,
                "use_id" => $user_id,
                "content" => $content,
            ]);
            $result = ["code" => SUCCESS, "msg" => "成功", "data" => $reply->id];
        }
        else {
            $result = ["code" => FAILURE, "msg" => "您还没有登录！"];
        }
        return json($result);
    }

    /**
     * Method history
     * @purpose 历史主页
     */
    public function history() {
        if ($user_id = session('id')){
            return $this->fetch();
        }
        else {
            $this->error("您还没有登录！");
        }
    }

    /**
     * Method uploadPage
     * @purpose ajax 上传页实现流加载
     */
    public function uploadPage()
    {
        if ($user_id = session('id')) {
            $num_per_page = 10;
            $page = $this->request->param('page');
            $files = File::where('use_id', $user_id)->order('id','desc')->limit($num_per_page)->page($page)->select();
            $count = File::where('use_id', $user_id)->select()->count();
            if ($count % $num_per_page == 0) {
                $pages = $count / $num_per_page;
            } else {
                $pages = (int)($count / $num_per_page) + 1;
            }
            $result = ["code" => SUCCESS, "msg" => "成功", "data" => $files, "pages" => $pages];
        }
        else {
                $result = ["code" => FAILURE, "msg" => "您还没有登录！"];
             }
        return json($result);
    }

    /**
     * Method questionTable
     * @purpose 获取历史问题表，分页形式
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function questionTable()  {
        $stu_id = session('id');
        if (!$stu_id) {
            // 适应layui接口
            $result = ["code" => 1, "msg" => "未登录！"];
        }
        else {
            $page = $this->request->param('page');
            $num_per_page = $this->request->param('limit');
            $questions_count = Question::where('stu_id',$stu_id)->select()->count();
            $questions = Question::where('stu_id',$stu_id)->order('id','desc')->limit($num_per_page)->page($page)->select();
            $data  = [];
            foreach ($questions as $key => $question) {
                $data[$key]['id'] = $question->id;
                $course_id = $question->course_id;
                $course = Course::get($course_id);
                $data[$key]['course_name'] = $course->name.'-'.$course->year.'-'.$course->semester;
                $data[$key]['title'] = $question->title;
                $data[$key]['read_count'] = $question->read_count;
                $data[$key]['status'] = $question->status;
                $data[$key]['create_time'] = $question->create_time;
                $data[$key]['update_time'] = $question->update_time;
            }
            // 适应layui接口
            $result = ["code" => 0, "msg" => "成功", "count" => $questions_count, "data" => $data];
        }
        return json($result);
    }

    /**
     * Method replyTable
     * @purpose 获取历史回复表，分页形式
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function replyTable()  {
        $use_id = session('id');
        if (!$use_id) {
            // 适应layui接口
            $result = ["code" => 1, "msg" => "未登录！"];
        }
        else {
            $page = $this->request->param('page');
            $num_per_page = $this->request->param('limit');
            $replies_count = Reply::where('use_id',$use_id)->select()->count();
            $replies = Reply::where('use_id',$use_id)->order('id','desc')->limit($num_per_page)->page($page)->select();
            $data  = [];
            foreach ($replies as $key => $reply) {
                $data[$key]['id'] = $reply->id;
                $que_id = $reply->que_id;
                $question = Question::get($que_id);
                $data[$key]['question_id'] = $question->id;
                $data[$key]['question_title'] = $question->title;
                $data[$key]['content'] = $reply->content;
                $data[$key]['status'] = $reply->status;
                $data[$key]['like_count'] = $reply->like_count;
                $data[$key]['dislike_count'] = $reply->dislike_count;
                $data[$key]['create_time'] = $reply->create_time;
                $data[$key]['update_time'] = $reply->update_time;
            }
            // 适应layui接口
            $result = ["code" => 0, "msg" => "成功", "count" => $replies_count, "data" => $data];
        }
        return json($result);
    }

    /**
     * Method replyDelete
     * @purpose 回复删除接口
     * @return \think\response\Json
     */
    public function replyDelete()  {
        $use_id = session('id');
        if (!$use_id) {
            $result = ["code" => FAILURE, "msg" => "未登录！"];
        }
        else {
            $reply_id =  $this->request->param('reply_id');
            $reply = Reply::get($reply_id);
            // 回复属主验证
            if ($reply_id === $reply->id) {
                $reply->delete();
                $result = ["code" => SUCCESS, "msg" => "成功删除!"];
            }
            else  {
                $result = ["code" => FAILURE, "msg" => "没有删除权限!"];
            }
        }
        return json($result);
    }

    /**
     * Method search
     * @purpose 搜索界面
     * @return mixed
     */
    public function search() {
        return $this->fetch();
    }

    /**
     * Method doSearch
     * @purpose 定向到搜索结果页面
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function doSearch() {
        $search_type = $this->request->param('search_type');
        $search_value = $this->request->param('search_value');
        $this->assign('search_type',$search_type);
        $this->assign('search_value',$search_value);
        $count = '';
        switch ($search_type) {
            case 'question':
                $count = Question::where('title','like','%'.$search_value.'%')->whereOr('content','like','%'.$search_value.'%')->select()->count();
                break;
            case 'reply':
                $count = Reply::where('content','like','%'.$search_value.'%')->select()->count();
                break;
            default:
                $result = ["code" => FAILURE, "msg" => "非法操作！"];
                break;
        }
        $this->assign('count', $count);
        return $this->fetch();
    }

    /**
     * Method searchPage
     * @purpose 搜索结果分页显示
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function searchPage() {
        $search_type = $this->request->param('search_type');
        $search_value = $this->request->param('search_value');
        $page = $this->request->param('page');
        $num_per_page = 10;
        switch ($search_type) {
            case 'question':
                $questions = Question::where('title','like','%'.$search_value.'%')->whereOr('content','like','%'.$search_value.'%')->order('id','desc')->limit($num_per_page)->page($page)->select();
                // 强调关联模型作为属性存在
                for ( $i = 0; $i < $questions->count(); $i++) {
                    $user = User::get($questions[$i]->stu_id);
                    $questions[$i]["user_name"] = $user->name;
                    $questions[$i]["user_avatar"] = $user->avatar;
                    $questions[$i]["user_role"] = $user->role;
                }
                $result = ["code" => SUCCESS, "msg" => "成功", "data" => $questions];
                break;
            case 'reply':
                $replies = Reply::where('content','like','%'.$search_value.'%')->order('id','desc')->limit($num_per_page)->page($page)->select();
                // 强调关联模型作为属性存在
                for ( $i = 0; $i < $replies->count(); $i++) {
                    $user = User::get($replies[$i]->use_id);
                    $replies[$i]["user_name"] = $user->name;
                    $replies[$i]["user_avatar"] = $user->avatar;
                    $replies[$i]["user_role"] = $user->role;
                }
                $result = ["code" => SUCCESS, "msg" => "成功", "data" => $replies];
                break;
            default:
                $result = ["code" => FAILURE, "msg" => "非法操作！"];
                break;
        }
        return json($result);
    }

    /**
     * Method create
     * @purpose 手动构造数据
     */
    public function create() {
//        for ( $i = 0; $i < 20; $i++) {
//            $reply = Reply::create([
//                'que_id' => '10004',
//                'use_id' => 'B00000',
//                'content' => "这是测试回答$i"
//            ]);
//            dump($reply->id);
//        }
        $user = User::create([
            'id' => 'B00002',
            'name' => '陈红松',
            'password' => password_hash('123456',PASSWORD_DEFAULT),
            'role' => 1,
            'email' => 'chenhongsong@ustb.edu.cn',
            'avatar' => 'http://scce.ustb.edu.cn/attach/file/shiziduiwu/jiaoshixinxi/2018-12-19/de760e256ec28ec0a7e6e6392e086f67.jpg',
            'profile' => ' 陈红松，目前，在北京科技大学计算机系工作，系副主任职务，副教授职称，硕士生导师，主要从事网络信息安全方面的教学和科研工作，陈红松目前是IEEE ComSoc Radio Communication 技术委员，Elsevier 出版的国际期刊《Information Sciences》特邀审稿人，Springer出版的国际期刊《Wireless Personal Communications》特邀审稿人，《北京邮电大学学报》审稿人、《应用科学学报》审稿人，中国计算机学会会员，中国密码学会会员，北京市通信协会IPv6 专委会委员。荣获2004年度哈工大计算机学院正在进行的优秀博士论文奖； 2005年度获得哈工大优秀博士生奖学金；2006年度被评为黑龙江省优秀博士毕业生；2007年获得北京科技大学优秀青年教师科研论文奖；2009年获得北京科技大学优秀科研论文奖；2010年获得北京科技大学第24届教育教学成果奖一等奖（排名第1）。2013年9月-2014年9月在美国普渡大学计算机科学系信息安全研究中心进行国家公派学术访问，主要从事网络内容安全、无线Ad hoc及传感器网络安全协议、云安全、大数据访问控制及行业应用、物联网安全等方向的研究。可以招收计算机科学与技术、软件工程、计算机技术等专业的硕士研究生。',

        ]);
        dump($user->id);
    }
}