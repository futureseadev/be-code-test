<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Organisation\CreateOrganisationRequest;
use App\Organisation;
use App\Services\OrganisationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class OrganisationController
 * @package App\Http\Controllers
 */
class OrganisationController extends ApiController
{
    /**
     * @param CreateOrganisationRequest $createOrganisationRequest
     * @param OrganisationService $service
     * @return JsonResponse
     */
    public function store(CreateOrganisationRequest $createOrganisationRequest, OrganisationService $service): JsonResponse
    {
        /** @var Organisation $organisation */
        $organisation = $service->createOrganisation($createOrganisationRequest->validated());

        return $this
            ->transformItem('organisation', $organisation, ['user'])
            ->respond();
    }

    /**
     * @param Request $request
     * @param OrganisationService $service
     * @return JsonResponse
     */
    public function listAll(Request $request, OrganisationService $service)
    {
        return $this->transformCollection(
            'organisations',
            $data = $service->listAll($request),
            ['user']
        )->withPagination($data)->respond();
    }

    public function listAllOld(OrganisationService $service)
    {
        $filter = $_GET['filter'] ?: false;
        $Organisations = DB::table('organisations')->get('*')->all();

        $Organisation_Array = [];

        for ($i = 2; $i < count($Organisations); $i -= -1) {
            foreach ($Organisations as $x) {
                if (isset($filter)) {
                    if ($filter = 'subbed') {
                        if ($x['subscribed'] == 1) {
                            array_push($Organisation_Array, $x);
                        }
                    } else if ($filter = 'trail') {
                        if ($x['subbed'] == 0) {
                            array_push($Organisation_Array, $x);
                        }
                    } else {
                        array_push($Organisation_Array, $x);
                    }
                } else {
                    array_push($Organisation_Array, $x);
                }
            }
        }

        return json_encode($Organisation_Array);
    }
}
