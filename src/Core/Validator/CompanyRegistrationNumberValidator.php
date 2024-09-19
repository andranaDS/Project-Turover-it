<?php

namespace App\Core\Validator;

use App\User\Enum\CompanyCountryCode;
use App\User\Service\Insee;
use App\User\Service\SocieteCom;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CompanyRegistrationNumberValidator extends ConstraintValidator
{
    private Insee $insee;
    private SocieteCom $societeCom;
    private EntityManagerInterface $em;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor, Insee $insee, SocieteCom $societeCom, EntityManagerInterface $em)
    {
        $this->insee = $insee;
        $this->em = $em;
        $this->propertyAccessor = $propertyAccessor;
        $this->societeCom = $societeCom;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CompanyRegistrationNumber) {
            throw new UnexpectedTypeException($constraint, CompanyRegistrationNumber::class);
        }

        // fetch values
        $countryCode = $this->propertyAccessor->getValue($value, $constraint->countryCodeProperty);
        $registrationNumber = $this->propertyAccessor->getValue($value, $constraint->registrationNumberProperty);

        if (empty($countryCode) || empty($registrationNumber)) {
            return;
        }

        // escape validation when registrationNumber && countryCode has not changed
        $oldUser = $this->em->getUnitOfWork()->getOriginalEntityData($value);
        if (\is_array($oldUser) && \array_key_exists($constraint->registrationNumberProperty, $oldUser)) {
            $oldCompanyRegistrationNumber = $oldUser[$constraint->registrationNumberProperty];
            if ($oldCompanyRegistrationNumber === $registrationNumber && \array_key_exists($constraint->countryCodeProperty, $oldUser)) {
                $oldCompanyCountryCode = $oldUser[$constraint->countryCodeProperty];
                if ($oldCompanyCountryCode === $countryCode) {
                    return;
                }
            }
        }

        // case FRANCE === check SIREN
        if (CompanyCountryCode::FR === $countryCode && null !== ($type = $constraint->parameters[CompanyCountryCode::FR]['type'] ?? null)) {
            $length = match ($type) {
                Insee::SIRENE_TYPE_SIREN => 9,
                Insee::SIRENE_TYPE_SIRET => 14,
                default => throw new \LogicException(sprintf('Property "type" must be "%s" or "%s" but "%s" provided.', Insee::SIRENE_TYPE_SIREN, Insee::SIRENE_TYPE_SIRET, $type)),
            };

            if (\strlen((string) $registrationNumber) !== $length) {
                $this->context->buildViolation($constraint->messageLength)
                    ->setParameter('{{ length }}', (string) $length)
                    ->atPath($constraint->registrationNumberProperty)
                    ->addViolation()
                ;

                return;
            }

            // check INSEE SIRENE V3 API
            $valid = Insee::SIRENE_TYPE_SIREN === $type ?
                $this->insee->isValidSiren($registrationNumber) :
                $this->insee->isValidSiret($registrationNumber);

            if (true === $valid) {
                return;
            }

            // if not found INSEE SIRENE V3 API, check SOCIETE.COM
            $valid = Insee::SIRENE_TYPE_SIREN === $type ?
                $this->societeCom->isValidSiren($registrationNumber) :
                $this->societeCom->isValidSiret($registrationNumber);

            if (true === $valid) {
                return;
            }

            $this->context->buildViolation($constraint->messageInvalid)
                ->setParameter('{{ registrationNumber }}', (string) $registrationNumber)
                ->atPath($constraint->registrationNumberProperty)
                ->addViolation()
            ;
        }
    }
}
