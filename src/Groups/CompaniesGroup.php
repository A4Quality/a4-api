<?php
/**
 * Created by PhpStorm.
 * User: r.a.freitas
 * Date: 20/10/2018
 * Time: 12:47
 */
use App\Basics\Account;
use App\Basics\Company;
use App\Utils\WorkOut;
use App\Controller\CompaniesController;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

$app->group('/companies', function () {

    $this->get('', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $companiesController = new CompaniesController();
        $workOut = new WorkOut();

        $return = $companiesController->findAll();
        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->post('', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();

        $company = new Company();
        $company->setName(isset($data['name']) ? $data['name'] : null);
        $company->setCnpj(isset($data['cnpj']) ? $workOut->removeMask($data['cnpj'], 'cnpj'): null);
        $company->setAnsRecord(isset($data['ansRecord']) ? $data['ansRecord'] : null);
        $company->setSegmentation(isset($data['segmentation']) ? $data['segmentation'] : null);
        $company->setContactPerson(isset($data['contactPerson']) ? $data['contactPerson'] : null);
        $company->setAddress(isset($data['address']) ? $data['address'] : null);
        $company->setEmail(isset($data['email']) ? $data['email'] : null);
        $company->setPhone(isset($data['phone']) ? $workOut->removeMask($data['phone'], 'phone'): null);
        $company->setActive(true);

        $company->setPort(isset($data['port']) ? $data['port'] : null);
        $company->setNumberOfEmployees(isset($data['numberOfEmployees']) ? $data['numberOfEmployees'] : null);
        $company->setNumberOfBeneficiaries(isset($data['numberOfBeneficiaries']) ? $data['numberOfBeneficiaries'] : null);
        $company->setIdss(isset($data['idss']) ? $data['idss'] : null);

        $company->setImage($workOut->base64_to_jpeg($data['file']['base64'], '', 'companies_img/'));

        $companiesController = new CompaniesController();
        $return = $companiesController->insert($company);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $data = $request->getParsedBody();

        $company = new Company();
        $company->setId(isset($data['id']) ? $data['id'] : null);
        $company->setName(isset($data['name']) ? $data['name'] : null);
        $company->setCnpj(isset($data['cnpj']) ? $workOut->removeMask($data['cnpj'], 'cnpj'): null);
        $company->setAnsRecord(isset($data['ansRecord']) ? $data['ansRecord'] : null);
        $company->setSegmentation(isset($data['segmentation']) ? $data['segmentation'] : null);
        $company->setContactPerson(isset($data['contactPerson']) ? $data['contactPerson'] : null);
        $company->setAddress(isset($data['address']) ? $data['address'] : null);
        $company->setEmail(isset($data['email']) ? $data['email'] : null);
        $company->setPhone(isset($data['phone']) ? $workOut->removeMask($data['phone'], 'phone'): null);

        $company->setPort(isset($data['port']) ? $data['port'] : null);
        $company->setNumberOfEmployees(isset($data['numberOfEmployees']) ? $data['numberOfEmployees'] : null);
        $company->setNumberOfBeneficiaries(isset($data['numberOfBeneficiaries']) ? $data['numberOfBeneficiaries'] : null);
        $company->setIdss(isset($data['idss']) ? $data['idss'] : null);

        if ($data['file'] && $data['file']['base64']) {
            $company->setImage($workOut->base64_to_jpeg($data['file']['base64'], '', 'companies_img/'));
        }

        $companiesController = new CompaniesController();
        $return = $companiesController->update($company);

        return $workOut->managerResponse($response, $return, 'result');
    });

    $this->put('/enabled', function (ServerRequestInterface $request, ResponseInterface $response, $args) {

        $workOut = new WorkOut();
        $data = $request->getParsedBody();

        $managerRequestToken = $workOut->managerRequestToken($request, $response, Account::GROUP_ADMIN);
        if ($managerRequestToken['status'] !== 200) return $managerRequestToken['response'];

        $company = new Company();
        $company->setId(isset($data['id']) ? $data['id'] : null);
        $company->setActive(isset($data['status']) ? $data['status'] : null);

        $companiesController = new CompaniesController();
        $return = $companiesController->enabled($company);

        return $workOut->managerResponse($response, $return, 'result');
    });
});