<?php

namespace App\Company\Validator;

use App\User\Enum\CompanyCountryCode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class CompanyIntracommunityVatValidator extends ConstraintValidator
{
    private EntityManagerInterface $em;
    private PropertyAccessorInterface $propertyAccessor;

    public function __construct(PropertyAccessorInterface $propertyAccessor, EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof CompanyIntracommunityVat) {
            throw new UnexpectedTypeException($constraint, CompanyIntracommunityVat::class);
        }

        // fetch values
        $countryCode = $this->propertyAccessor->getValue($value, 'billingAddress.countryCode');
        $intracommunityVat = $this->propertyAccessor->getValue($value, 'intracommunityVat');
        $registrationNumber = $this->propertyAccessor->getValue($value, 'registrationNumber');

        if (empty($intracommunityVat) || empty($registrationNumber)) {
            return;
        }

        // escape validation when intracommunityVat && registrationNumber has not changed
        $oldCompany = $this->em->getUnitOfWork()->getOriginalEntityData($value);
        if (\is_array($oldCompany) && \array_key_exists('intracommunityVat', $oldCompany)) {
            $oldCompanyIntracommunityVat = $oldCompany['intracommunityVat'];
            if ($oldCompanyIntracommunityVat === $intracommunityVat && \array_key_exists('registrationNumber', $oldCompany)) {
                $oldCompanyRegistrationNumber = $oldCompany['registrationNumber'];
                if ($oldCompanyRegistrationNumber === $registrationNumber) {
                    return;
                }
            }
        }

        // case FRANCE === check intracommunityVat
        if (CompanyCountryCode::FR === $countryCode) {
            // @Author Elise
            $siren = substr($registrationNumber, 0, 9);
            $key = (12 + 3 * (int) $siren % 97) % 97;
            if ($key > 10) {
                $vat = 'FR' . $key . $siren;
            } elseif ($key > 1) {
                $vat = 'FR0' . $key . $siren;
            } else {
                $vat = 'FR00' . $siren;
            }

            if ($vat !== $intracommunityVat) {
                $this->context->buildViolation($constraint->messageInvalid)
                    ->setParameter('{{ intracommunityVat }}', (string) $intracommunityVat)
                    ->atPath('intracommunityVat')
                    ->addViolation()
                ;
            }
        }
    }
}
