<?php
namespace App\Connection;
use PHPMailer\PHPMailer\PHPMailer;
use PDO;
class Database
{
    /**
     *
     * @var /PDO
     */
    protected static $db;
    private function __construct()
    {
        $db_host = Credentials::getHost();
        $db_nome = Credentials::getDbname();
        $db_usuario = Credentials::getUser();
        $db_senha = Credentials::getPassword();
        $db_driver = 'mysql';
        $param = [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'set lc_time_names="pt_BR";',
            //PDO::MYSQL_ATTR_INIT_COMMAND => 'set time_zone = "America/Recife"'
        ];
        $sistema_titulo = "API - A4";
        $sistema_email = "rafael.vasconcelos@outlook.com";

        try {
            self::$db = new PDO(
                "$db_driver:host=$db_host; dbname=$db_nome",
                $db_usuario,
                $db_senha,
                $param
            );
            self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$db->exec('SET NAMES utf8');
        } catch (\PDOException $ex) {
            $mail = new PHPMailer(true);
            $mail->CharSet = 'UTF-8';
            $mail->IsSMTP();
            $mail->Host = "mx1.hostinger.com.br";
            $mail->SMTPAuth = true;
            $mail->Port = 587;
            $mail->Username = 'no-reply@a4.rafafreitas.com';
            $mail->Password = 'slQ0F9r5xSsd';
            $mail->From = "noreply@a4.com.br";
            $mail->FromName = "API - A4-App";
            $mail->AddAddress($sistema_email, 'Contato');
            $mail->Subject = "PDOException em $sistema_titulo";
            $mail->MsgHTML($ex->getMessage());
            $mail->IsHTML(false);
            $mail->Send();

            $return = [
                'status' => 500,
                'message' => "ERROR",
                'result' => 'Erro na execução da instrução!',
                'CODE' => $ex->getCode(),
                'Exception' => $ex->getMessage(),
            ];
            die(json_encode($return));
        }
    }
    public static function conexao()
    {
        if (!self::$db) {
            new Database();
        }
        return self::$db;
    }
}
