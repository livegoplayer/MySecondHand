<?php
/**
// * Created by PhpStorm.
// * User: xjyplayer
// * Date: 2018/11/6
// * Time: 21:16
// */
namespace app\common\lib\email;

use PHPMailer\PHPMailer\PHPMailer;
use think\Exception;

class Email
{
    /**
     * 发送邮件
     * @param $sendee_email
     * @param $title
     * @param $msg
     * @return bool|string
     * @throws \PHPMailer\PHPMailer\Exception
     */
    public static function sendMsg($sendee_email,$title,$msg)
    {
        date_default_timezone_set('PRC');   //设置时区
        if(empty($sendee_email)){
            return false;
        }
        try {
            $mail = new PHPMailer;
            //Tell PHPMailer.class to use SMTP
            $mail->isSMTP();
            //Enable SMTP debugging
            // 0 = off (for production use)
            // 1 = client messages
            // 2 = client and server messages
            //$mail->SMTPDebug = 2;
            //Ask for HTML-friendly debug output
            $mail->Debugoutput = 'html';
            //Set the hostname of the mail server
            $mail->Host = config('email.host');
            //Set the SMTP port number - likely to be 25, 465 or 587
            $mail->Port = config('email.port');
            //Whether to use SMTP authentication
            $mail->SMTPAuth = true;
            //Username to use for SMTP authentication
            $mail->Username = config('email.username');
            //Password to use for SMTP authentication
            $mail->Password = config('email.validate_password');
            //Set who the message is to be sent from
            $mail->setFrom(config('email.sender_email'), config('email.sender_name'));
            //Set an alternative reply-to address
            //$mail->addReplyTo('replyto@example.com', 'First Last');
            //Set who the message is to be sent to
            $mail->addAddress($sendee_email, '');
            //Set the subject line
            $mail->Subject = $title;
            //Read an HTML message body from an external file, convert referenced images to embedded,
            //convert HTML into a basic plain-text alternative body
            $mail->msgHTML($msg);
            //Replace the plain text body with one created manually
            //$mail->AltBody = 'This is a plain-text message body';
            //Attach an image file
            //$mail->addAttachment('images/phpmailer_mini.png');

            //send the message, check for errors
            if (!$mail->send()) {
                return $mail->ErrorInfo;
            } else {
                return true;
            }
        }catch (Exception $exception){
            return false;
        }
    }

}