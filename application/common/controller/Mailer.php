<?php
namespace app\common\controller;
// Import PHPMailer classes into the global namespace
// These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
//require 'vendor/autoload.php';

/**
 * Class Mailer
 * @package app\common\controller
 * @author liuyuhan
 */
class Mailer
{
    protected $mailer;

    /**
     * Mailer constructor.
     */
    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->mailer->CharSet    = 'UTF-8';
        $this->mailer->setLanguage('ch', '../vendor/phpmailer/language/');
        $this->mailer->isSMTP();
        $this->mailer->Host       = 'smtp.exmail.qq.com';  // Specify main and backup SMTP servers
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = 'support@dfface.com';
        $this->mailer->Password   = 'gWEJjBLdAiqYJbsF';
        $this->mailer->SMTPSecure = 'ssl';
        $this->mailer->Port       = 465;
        $this->mailer->setFrom('support@dfface.com','OAS在线答疑系统');
    }

    /**
     * Method setRecipient
     * @purpose 设置收件人
     * @param $mail
     * @param $name
     */
    public function setRecipient($mail,$name) {
        $this->mailer->addAddress($mail,$name);
    }

    /**
     * Method setCode
     * @purpose 设置找回密码邮件
     * @param $code
     * @param $name
     * @param $id
     */
    public function setCode($code,$name,$id) {
        $this->mailer->isHTML(true);
        $this->mailer->Subject = "重要！在线答疑系统密码找回";
        $this->mailer->Body = "<h3>亲爱的".$name."，您好：</h3>"
        ."您的帐号 ".$id." 正在申请密码找回。"
        ."<p>您的验证码是：<span style=\"font-size: 3rem\">".$code."</span> ，该验证码将于1小时后失效。</p>"
        ."<p>本邮件由系统自动发送，请不要直接回复！</p>"
        ."<p>© 2018-2019 北京科技大学 OAS软工课设小组</p>";
    }

    /**
     * Method setReplyRemind
     * @purpose 设置教师回答后自动给学生发送提醒邮件的内容
     * @param $tea_name
     * @param $stu_name
     * @param $que_id
     */
    public function setReplyRemind($tea_name,$stu_name,$que_id) {
        $this->mailer->isHTML(true);
        $this->mailer->Subject = "你的问题得到".$tea_name."老师的回复了";
        $this->mailer->Body = "<h3>亲爱的".$stu_name."，你好：</h3>"
            ."你的问题 ".$que_id." 已有老师"."<span style='color: orangered;'>".$tea_name."</span>"."做出回答，快去看看吧。"
            ."<p>本邮件由系统自动发送，请不要直接回复！</p>"
            ."<p>© 2018-2019 北京科技大学 OAS软工课设小组</p>";
    }

    /**
     * Method send
     * @purpose 发送邮件
     * @throws Exception
     */
    public function send() {
        $this->mailer->send();
    }

}