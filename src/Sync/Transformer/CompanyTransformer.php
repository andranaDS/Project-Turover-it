<?php

namespace App\Sync\Transformer;

use App\Company\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;

class CompanyTransformer
{
    private array $matches;

    public function __construct(EntityManagerInterface $em)
    {
        $this->matches = $em->getRepository(Company::class)->findMatches();
    }

    public function transform(?string $inValue, bool $required = false, ?string &$error = null): ?int
    {
        if (empty($inValue)) {
            if (true === $required) {
                $error = 'required';
            }

            return null;
        }

        if (null === ($newCompanyId = $this->matches[$inValue] ?? null)) {
            $error = sprintf('"%s" was not found in the database', $inValue);

            return null;
        }

        return $newCompanyId;
    }
}
