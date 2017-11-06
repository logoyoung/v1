<?php

/**

$mail->setFrom('from@example.com', 'Mailer');
$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient
$mail->addAddress('ellen@example.com');               // Name is optional
$mail->addReplyTo('info@example.com', 'Information');
$mail->addCC('cc@example.com');
$mail->addBCC('bcc@example.com');

$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
$mail->isHTML(true);                                  // Set email format to HTML

$mail->Subject = 'Here is the subject';
$mail->Body    = 'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

if(!$mail->send()) {
    echo 'Message could not be sent.';
    echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    echo 'Message has been sent';
}
 * 
 */

namespace HP\Service;

composer_autoload();
class OpMail {
    
    static public function send($address,$subject,$body,$type=0){
        $obj = $type?self::factoryJSB():self::factorySRV();
        if(is_array($address)){
            foreach ($address as $item){
                $obj->addAddress($item);
            }
        }else{
            $obj->addAddress($address);
        }
        $obj->Subject = $subject;
        $obj->isHTML();
        $obj->Body = $body;
        if($obj->send()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 响当当
     * @staticvar self $obj
     * @return \self
     */
    static public function factorySRV() {
        static $obj;
        if (!$obj) {
            $obj = new \PHPMailer();
            $obj->isSMTP();
            $obj->Host = 'smtp.163.com';
            $obj->SMTPAuth = true;
            $obj->FromName = '欢朋';
            $obj->From = 'huanpeng_op@163.com';
            $obj->Username = 'huanpeng_op@163.com';
            $obj->Password = '5VR048CP';
            $obj->SMTPSecure = 'tls';
            $obj->Port = 25;   
            $obj->CharSet = 'UTF-8';
            $obj->Encoding = "base64";
            $obj->isHTML(true); 
        }
        return $obj;
    }

    /**
     * 响当当技术支持
     * @staticvar self $obj
     * @return \self
     */
    static public function factoryJSB() {
        static $obj;
        if (!$obj) {
            $obj = new \PHPMailer();
            $obj->isSMTP();
            $obj->Host = 'smtp.163.com';
            $obj->SMTPAuth = true;
            $obj->FromName = '欢朋';
            $obj->From = 'huanpeng_op@163.com';
            $obj->Username = 'huanpeng_op@163.com';
            $obj->Password = '5VR048CP';
            $obj->SMTPSecure = 'tls';
            $obj->Port = 25;   
            $obj->CharSet = 'UTF-8';
            $obj->Encoding = "base64";
            $obj->isHTML(true); 
        }
        return $obj;
    }
    
}
