<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email
{

	public static function configEmail($data, $embeddedImage = false){

        try {

           require_once ROOT . "libs" . DIR_SEP . "Exception.php";         
           require_once ROOT . "libs" . DIR_SEP . "PHPMailer.php";         
           require_once ROOT . "libs" . DIR_SEP . "SMTP.php";         

            //Server settings
            $mail = new PHPMailer(true);
            $mail->IsSMTP();
            $mail->CharSet="UTF-8";
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                )
            );
            $mail->Host = Security::decode(MAIL_SMTP,MAIL_KEYHASH);
            $mail->Port = MAIL_PORT;
            $mail->Username = Security::decode(MAIL_USER,MAIL_KEYHASH);
            $mail->Password = Security::decode(MAIL_PASSWORD, MAIL_KEYHASH);
            $mail->SMTPAuth = true;
            
            $mail->SetFrom($data['remitente']['email'], $data["remitente"]['name']);
            $mail->addAddress($data['receptor']['email'], $data['receptor']['name']);

            //Contenido
            $mail->isHTML($data['isHTML']); // Set email format to HTML
            // $mail->AddEmbeddedImage(BASE_URL . 'public/img/logo.png', 'logoimg', BASE_URL . 'public/img/logo.jpg'); // Send Logo 
            $mail->Subject = $data['Asunto'];
            $mail->Body    = $data['Contenido'];
            $mail->AltBody = $data['Contenido-noHTML'];

            if($embeddedImage){
                $mail->AddEmbeddedImage($embeddedImage["path"], $embeddedImage["cid"]);                
            }

            return $mail->send();

        } catch (Exception $e) {
            return $e->getMessag();
        }
    }
}