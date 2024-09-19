<?php

namespace App\Partner\Validator;

use App\Partner\Entity\Partner;
use App\Partner\Enum\Partner as PartnerEnum;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class PartnerDistributionValidator extends ConstraintValidator
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof PartnerDistribution) {
            throw new UnexpectedTypeException($constraint, PartnerDistribution::class);
        }

        if (!$value instanceof Partner) {
            throw new UnexpectedTypeException($constraint, Partner::class);
        }

        if (null === $partnerNone = $this->entityManager->getRepository(Partner::class)->findOneByPartner(PartnerEnum::NONE)) {
            throw new \LogicException(PartnerEnum::NONE . ' partner not found.');
        }

        if (null === $value->getId()) {
            return;
        }

        /** @var array $oldPartner */
        $oldPartner = $this->entityManager->getUnitOfWork()->getOriginalEntityData($value);

        if ($oldPartner['distribution'] > $value->getDistribution() && ($value->getDistribution() - $oldPartner['distribution'] > $partnerNone->getDistribution())) {
            $this->context->buildViolation($constraint->messageDistributionInvalid)
                ->setParameter('%value%', (string) ($partnerNone->getDistribution() + $value->getDistribution()))
                ->atPath('distribution')
                ->addViolation()
            ;
        }
    }
}
