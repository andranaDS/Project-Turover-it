<?php

namespace App\User\Controller\FreeWork\CompanyCountryCode;

use App\User\Enum\CompanyCountryCode;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class Get
{
    /**
     * @Route(
     *     name="api_user_freework_company_country_code_get",
     *     path="/company_country_codes",
     *     methods={"GET"},
     *     condition= "request.headers.get('Host') matches '%api_free_work_pattern_base_url%'",
     * )
     * @Cache(smaxage="7200", maxage="0")
     */
    public function __invoke(Request $request, TranslatorInterface $translator): JsonResponse
    {
        $companyCountryCodes = CompanyCountryCode::getConstants();

        $data = array_map(static function (string $country) use ($translator) {
            return $translator->trans('app_user_enum_company_country_code_' . strtolower($country), [], 'enums');
        }, $companyCountryCodes);

        $collator = new \Collator('fr_FR');
        $collator->asort($data);

        $fra = $data['FR'];
        $ooo = $data['OO'];

        unset($data['FR'], $data['OO']);

        $data = ['FR' => $fra] + $data + ['OO' => $ooo];

        return new JsonResponse($data);
    }
}
