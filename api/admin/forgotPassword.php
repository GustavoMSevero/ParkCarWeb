<?php
header("Access-Control-Allow-Origin: *");
ini_set('display_errors', true);
error_reporting(E_ALL);

include_once("../con.php");
include_once("../functions/jwt.php");

$pdo = conectar();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

$data = file_get_contents("php://input");
$data = json_decode($data);

if($data){
	$option = $data->option;
}else{
	$option = $_GET['option'];
}

switch ($option) {
      case 'forgot password':
        // print_r($data);

        $email = $data->email;

        try {

            $checkIfEmailExists=$pdo->prepare("SELECT * FROM ownerParking WHERE ownerEmail=:email");
            $checkIfEmailExists->bindValue(":email", $email);
            $checkIfEmailExists->execute();

            $exists = $checkIfEmailExists->rowCount();

            if ($exists != 0) {
            
                while ($line=$checkIfEmailExists->fetch(PDO::FETCH_ASSOC)) {
                    $email = $line['ownerEmail'];
                    $parkingName = $line['parkingName'];
                }

                // echo $email;
                $parkingEmail = $email;

                $mail = new PHPMailer();
                $mail->isSMTP();
                $mail->Host       = 'smtp.uni5.net';
                // $mail->Host       = 'smtp.parkcar.app.br';
                $mail->SMTPAuth   = true; 
                $mail->Username   = 'recuperacaosenha@parkcar.app.br';
                $mail->Password   = 'STbv@2021';
                $mail->Port       = '587';
                $image = '../imgs/logo.png';
                // <img src='cid:".$image." width='100' height='50' >

                //Recipients
                $site = 'ParkCar';
                $mail->setFrom('recuperacaosenha@parkcar.app.br', $site);
                $mail->addAddress($email, $parkingName);

                // Content
                $mail->Subject = "RECUPERAÇÃO DE SENHA PARKCAR";
                $mail->Body    = "<html lang='en'>
                                    <head>
                                        <meta charset='UTF-8'>
                                    </head>
                                    <body>
                                        <p>Olá, ".$parkingName.".<br>
                                        Recebemos um pedido de recuperação de senha!</p>
                                        <a href ='http://www.parkcar.app.br'>http://www.parkcar.app.br</a>
                                        
                                    </body>
                                </html>";
                $mail->IsHTML(true); // Set email format to HTML

                $mail->send();
                echo 'Message has been sent';
                $msg = 'E-mail de boas-vindas enviada com sucesso para '.$parkingName.'! '.$parkingEmail;
                $msgRegisterOK = 'Estacionamento '.$parkingName.' adicionado com sucesso';

                $return = array(
                    'msg' => $msg,
                    'msgRegisterOK' => $msgRegisterOK
                );

                echo json_encode($return);

            } else {

                $status = 0;
                $msg = 'Email inexistente';

                $return = array(
                    'status' => $status,
                    'msg' => utf8_encode($msg)
                );

                echo json_encode($return);
           
            }

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
		
        break;

    default:
        # code...
        break;
}


?>