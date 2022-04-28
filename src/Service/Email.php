<?php

namespace App\Service;
use App\Basics\Account;
use PHPMailer\PHPMailer\PHPMailer;

class Email
{
    private $charset = 'UTF-8';
    private $host = "smtp.kinghost.net";
    private $port = 587;
    private $fromName = "A4Quality HealthCare";
    private $fromEmail = 'no-reply@a4quality.com';
    private $userName = 'no-reply@a4quality.com';
    private $password = 'ayqLrVZpQG5WqCUizifV';
    private $subject;

    // api@@a4quality.com
    // a5YOtKGcrKo840trBGWS

    /**
     * SendEmail constructor.
     * @param $subject
     */
    public function __construct($subject)
    {
        $this->subject = $subject;
    }

    public function send($address, $body ){

        try {

            $mail = new PHPMailer(true);
            $mail->CharSet = $this->charset;
            $mail->IsSMTP();
            $mail->Host = $this->host;
            // $mail->SMTPSecure = 'ssl';
            $mail->SMTPAuth = true;
            $mail->Port = $this->port;
            $mail->Username = $this->userName;
            $mail->Password = $this->password;
            $mail->FromName = $this->fromName;
            $mail->From = $this->fromEmail;
            $mail->Subject  = $this->subject;

            if (is_array($address)) {
                foreach ($address as $add){
                    $mail->AddAddress($add, 'Contato');
                }
            }else{
                $mail->AddAddress($address, 'Contato');
            }

            $mail->MsgHTML($body);
            $mail->IsHTML(true);
            $mail->Send();

            if($mail){
                return array(
                    'status'    => 200,
                    'message'   => "SUCCESS",
                    'result'    => 'E-mails enviados com sucesso!',
                );
            }else{
                return array(
                    'status'    => 500,
                    'message'   => "ERROR",
                    'result'    => 'Erro na execução da instrução!',
                    'CODE'      => 'Code não informado',
                    'Exception' => 'Ex não informada'
                );
            }

        }catch (\Exception $ex){
            return array(
                'status'    => 500,
                'message'   => "ERROR",
                'result'    => 'Erro na execução da instrução!',
                'CODE'      => $ex->getCode(),
                'Exception' => $ex->getMessage()
            );
        }
    }

    public function newPass(Account $account, $reset, $senha)
    {
        try {
            if ($reset) {
                $body = file_get_contents(
                    __DIR__ . '/../Utils/EmailsTemplates/ResetPass.html'
                );
                $this->subject = "Nova senha gerada!";
            } else {
                $body = file_get_contents(
                    __DIR__ . '/../Utils/EmailsTemplates/Welcome.html'
                );
                $this->subject = "Acesse agora a A4!";
            }

            $body = str_replace('%NOVA_SENHA%', $senha, $body);
            return $this->send($account->getEmail(), $body);
        } catch (\Exception $ex) {
            return [
                'status' => 500,
                'message' => "ERROR",
                'result' => 'Erro na execução da instrução!',
                'CODE' => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            ];
        }
    }

}