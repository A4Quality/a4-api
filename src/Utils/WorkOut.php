<?php

namespace App\Utils;

use App\Basics\Account;
use App\Basics\Admin;
use App\Basics\Company;
use App\Basics\CompanyUser;
use App\Basics\Evaluator;
use App\Config\Authorization;
use App\Config\Doctrine;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class WorkOut
{

    public function managerRequestToken (ServerRequestInterface $request, ResponseInterface $response, $group = null) {

        $auth = new Authorization();
        $objJwt = $auth->validateToken($request);

        if ($objJwt['status'] != 200)
            return [
                'status' => 401,
                'response' => $response->withJson($objJwt, $objJwt['status'])
            ];

        if ($group && !is_array($group) && $objJwt['token']->data->gr_id !== $group)
            return [
                'status' => 401,
                'response' => $response->withJson(
                    [
                        'status' => 401,
                        'message' => "ERROR",
                        'result' =>
                            "Você não tem permissão para visualizar este conteúdo",
                    ],
                    401
                )
            ];

        if ($group && is_array($group) && !in_array($objJwt['token']->data->gr_id, $group))
            return [
                'status' => 401,
                'response' => $response->withJson(
                    [
                        'status' => 401,
                        'message' => "ERROR",
                        'result' =>
                            "Você não tem permissão para visualizar este conteúdo",
                    ],
                    401
                )
            ];

        return $objJwt;
    }

    public function managerResponseToken (ResponseInterface $response, $return, $param) {

        if ($return['status'] !== 200){
            return $response->withJson($return, $return['status']);
        }else{
            $auth = new Authorization();

            $content = array(
                'us_id' => $return['user']['id'],
                'ac_id' => $return['user']['account']['id'],
                'gr_id' => $return['user']['account']['groupId'],
            );

            $jwt = $auth->createToken($content);

            return $response
                ->withHeader('Authorization', $jwt)
                ->withJson($return[$param], 200);
        }

    }

    public function managerResponse (ResponseInterface $response, $return, $param) {

        return ($return['status'] !== 200) ?
            $response->withJson($return, $return['status']) :
            $response->withJson($return[$param], 200);

    }

    public function getData()
    {
        date_default_timezone_set("America/Recife");
        return date('Y-m-d H:i:s');
    }

    public function checkCPF($cpf, $group, $isEdit = false, $oldCpf = null)
    {
        $doctrine = new Doctrine();
        $entityManager = $doctrine->getEntityManager();

        $returnError = [
            'status' => 400,
            'message' => "ERROR",
            'result' => 'Este cpf já esta em uso por outra conta!',
        ];
        $returnSuccess = ['status' => 200];

        switch ($group) {
            case Account::GROUP_ADMIN:
                $obj = $entityManager->getRepository(Admin::class)
                    ->findBy(['cpf' => $cpf], ['id' => 'ASC'], 1);
                break;
            case Account::GROUP_EVALUATOR:
                $obj = $entityManager->getRepository(Evaluator::class)
                    ->findBy(['cpf' => $cpf], ['id' => 'ASC'], 1);
                break;
            case Account::GROUP_COMPANY_USER:
                $obj = $entityManager->getRepository(CompanyUser::class)
                    ->findBy(['cpf' => $cpf], ['id' => 'ASC'], 1);
                break;
            default:
                $returnError['result'] = 'CPF não informado!';
                return $returnError;
        }

        if (!empty($obj) && !$isEdit) {
            return $returnError;
        } else if (!empty($obj) && $isEdit && $obj[0]->getCpf() !== $oldCpf) {
            return $returnError;
        } else {
            return $returnSuccess;
        }
    }

    public function checkCNPJ($cnpj, $isEdit = false, $oldCnpj = null)
    {
        $doctrine = new Doctrine();
        $entityManager = $doctrine->getEntityManager();

        $returnError = [
            'status' => 400,
            'message' => "ERROR",
            'result' => 'Este CNPJ já esta em uso por outra empresa!',
        ];
        $returnSuccess = ['status' => 200];

        $obj = $entityManager->getRepository(Company::class)
            ->findBy(['cnpj' => $cnpj], ['id' => 'ASC'], 1);

        if (!empty($obj) && !$isEdit) {
            return $returnError;
        } else if (!empty($obj) && $isEdit && $obj[0]->getCnpj() !== $oldCnpj) {
            return $returnError;
        } else {
            return $returnSuccess;
        }
    }

    public function removeMask($oldValue, $type)
    {
        switch ($type) {
            case 'cpf':
                $cpf = str_replace(".", "", $oldValue);
                $cpf = str_replace("-", "", $cpf);
                return $cpf;
                break;

            case 'cnpj':
                $cnpj = str_replace(".", "", $oldValue);
                $cnpj = str_replace("-", "", $cnpj);
                $cnpj = str_replace("/", "", $cnpj);
                return $cnpj;
                break;

            case 'phone':
                $telefone = str_replace("(", "", $oldValue);
                $telefone = str_replace(")", "", $telefone);
                $telefone = str_replace(" ", "", $telefone);
                $telefone = str_replace("-", "", $telefone);
                return $telefone;
                break;

            case 'money':
                $money = str_replace("R$ ", "", $oldValue);
                $money = str_replace(".", "", $money);
                $money = str_replace(",", ".", $money);
                return $money;
                break;
            case 'cep':
                $cep = str_replace(".", "", $oldValue);
                $cep = str_replace("-", "", $cep);
                $cep = $this->mask($cep, '#####-###');
                return $cep;
                break;
            case 'cep_only_number':
                $cep = str_replace(".", "", $oldValue);
                $cep = str_replace("-", "", $cep);
                return $cep;
                break;
        }
    }

    function mask($val, $mask)
    {
        $maskared = '';
        if (empty($val)) return $maskared;
        $k = 0;
        for ($i = 0; $i <= strlen($mask) - 1; $i++) {
            if ($mask[$i] == '#') {
                if (isset($val[$k])) {
                    $maskared .= $val[$k++];
                }
            } else {
                if (isset($mask[$i])) {
                    $maskared .= $mask[$i];
                }
            }
        }
        return $maskared;
    }

    function prepareListUserActiveInactive ($obj) {
        if (empty($obj)) {
            return [
                'status' => 200,
                'message' => "SUCCESS",
                'qtd' => 0,
                'result' => [
                    'active' => [],
                    'inactive' => [],
                ]
            ];
        } else {
            $workOut = new WorkOut();
            $users = [
                'active' => [],
                'inactive' => [],
            ];
            foreach ($obj as $user) {
                $temp = $user->convertArray();

                $temp['cpf'] = $workOut->mask($temp['cpf'],'###.###.###-##');
                $temp['account'] = $user->getAccount()->convertArray();

                if ($user->getAccount()->getActive()) {
                    array_push($users['active'], $temp);
                } else {
                    array_push($users['inactive'], $temp);
                }
            }

            return [
                'status' => 200,
                'message' => "SUCCESS",
                'qtd' => count($obj),
                'result' => $users,
            ];
        }
    }

    public function getPercent($percent, $value)
    {
        return ($percent * $value) / 100;
    }

    function base64_to_jpeg($base64_string, $output_file, $folder)
    {
        try {
            if (empty($base64_string)) {
                return null;
            }
            $output_file = strtoupper(uniqid()) . $output_file;
            $image_parts = explode(";base64,", $base64_string);
            $image_type_aux = explode("image/", $image_parts[0]);
            $image_type = $image_type_aux[1];
            $image_base64 = base64_decode($image_parts[1]);
            $file = UPLOAD_CLI . $folder . $output_file . '.' . $image_type;
            file_put_contents($file, $image_base64);
            return $output_file . '.' . $image_type;
        } catch (\Exception $ex) {
            var_dump($ex);
            return '';
        }
    }

    function base64_to_file($base64_string, $name, $ext, $folder)
    {
        try {
            if (empty($base64_string)) {
                return null;
            }
            $output_file = $name .'_-_-_' . strtoupper(uniqid());
            $base64 = explode(";base64,", $base64_string);
            $decode = base64_decode($base64[1]);
            $file = UPLOAD_CLI . $folder .'/'. $output_file . '.' . $ext;
            file_put_contents($file, $decode);
            return $output_file . '.' . $ext;
        } catch (\Exception $ex) {
            var_dump($ex);
            return '';
        }
    }

    function get12Months() {
        date_default_timezone_set('UTC');

        $mons = array(
            -11 => "Janeiro", -10 => "Fevereiro",
            -9 => "Março", -8 => "Abril",
            -7 => "Maio", -6 => "Junho",
            -5 => "Julho",-4 => "Agosto",
            -3 => "Setembro",-2 => "Outubro",
            -1 =>"Novembro", 0 =>"Dezembro",
            1 => "Janeiro", 2 => "Fevereiro",
            3 => "Março", 4 => "Abril",
            5 => "Maio", 6 => "Junho",
            7 => "Julho",8 => "Agosto",
            9 => "Setembro", 10 => "Outubro",
            11 => "Novembro", 12 => "Dezembro"
        );

        $date = getdate();
        $month = $date['mon'];
        $last_12_months = [];

        for ($i = 0; $i >= -11; $i--) {
            $actual = $mons[$month];

            if ($i === 0) {
                $firstDay = date("Y-m-d", strtotime("first day of this month"));
                $lastDay = date("Y-m-d", strtotime("last day of this month"));
                $lastDay .= " 23:59:59";
            } else if ($i === -1) {
                $firstDay = date("Y-m-d", strtotime("first day of previous month"));
                $lastDay = date("Y-m-d", strtotime("last day of previous month"));
                $lastDay .= " 23:59:59";
            } else {
                $firstDay = date("Y-m-d", strtotime("first day of ".$i." month"));
                $lastDay = date("Y-m-d", strtotime("last day of ".$i." month"));
                $lastDay .= " 23:59:59";
            }

            $temp = [
                'month' => $actual,
                'firstDay' => $firstDay,
                'lastDay' => $lastDay,
            ];

            array_push($last_12_months, $temp);
            $month--;
        }

        return $last_12_months;

    }
}