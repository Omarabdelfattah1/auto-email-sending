<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require_once 'models/Post.php';
require_once 'models/User.php';
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'Database.php';
require_once 'config.php';

$postModel=new Post;
$userModel=new User;
function users_to_email()
{
  $posts=$postModel->getPostsFromLastWeek();
  $users_ids = array_column($posts, 'user_id');
  $users=$userModel->getUsers();
  $users_to_email=array();
  foreach($users as $user)
  {
    if(!in_array($user['id'],$users_ids))
    {
      $item=array(
        'id'=>$user['id'],
        'name'=>$user['name'],
        'email'=>$user['email']
      );
      array_push($users_to_email,$item);
    }
  }
  return $users_to_email;
}

$mail=new PHPMailer();
$users=users_to_email();
foreach($users as $user)
{
  try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                   
    $mail->isSMTP();                                         
    $mail->Host       = 'smtp.gmail.com';                    
    $mail->SMTPAuth   = true;                                   
    $mail->Username   = 'app_email@gmail.com';    //email you will use to send from to users                 
    $mail->Password   = 'app_email_password';      //password                          
    $mail->SMTPSecure = 'ssl';         
    $mail->Port       = 465;                                  

    $mail->setFrom('no-reply@agency.com', 'Agency');
    $mail->addAddress($user['email'], $user['name']);    
    $mail->isHTML(true);                                  
    $mail->Subject = 'We miss you.';
    $mail->Body    = 'Hello <b>.'.$user['name'].'</b>Long Time u didn\'t post at our App';

    $mail->send();
    echo 'Message has been sent';
  } catch (Exception $e) {
      echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}

