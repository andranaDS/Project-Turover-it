<?php

namespace App\Core\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Healthz
{
    /**
     * @Route(name="api_core_healthz", path="/healthz", methods={"GET"})
     * @Cache(smaxage="0", maxage="0")
     */
    public function __invoke(): Response
    {
        return new Response();
    }
}
