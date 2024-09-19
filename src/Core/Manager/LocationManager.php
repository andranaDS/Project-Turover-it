<?php

namespace App\Core\Manager;

use App\Core\Entity\Location;
use App\Core\Entity\LocationKeyLabel;
use App\Core\Util\Arrays;
use App\Core\Util\Strings;
use App\Sync\Repository\FreeWorkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Geocoder\Collection;
use Geocoder\Exception\InvalidArgument;
use Geocoder\Exception\InvalidServerResponse;
use Geocoder\Exception\QuotaExceeded;
use Geocoder\Model\Address;
use Geocoder\Provider\Provider;
use Geocoder\Query\GeocodeQuery;

class LocationManager
{
    private Provider $provider;
    private EntityManagerInterface $em;
    private FreeWorkRepository $freeWorkRepository;

    public function __construct(Provider $locationIQGeocoder, EntityManagerInterface $em, FreeWorkRepository $freeWorkRepository)
    {
        $this->provider = $locationIQGeocoder;
        $this->em = $em;
        $this->freeWorkRepository = $freeWorkRepository;
    }

    public function autocomplete(string $search, int $limit = 5, ?string $tag = null, ?string $countryCodes = null, ?string $viewBox = null): array
    {
        $query = GeocodeQuery::create($search)
            ->withLocale('fr')
            ->withData('dedupe', 1)
            ->withData('autocomplete', true)
        ;

        if (!empty($limit)) {
            $query = $query->withLimit($limit);
        }

        if (!empty($tag)) {
            $query = $query->withData('tag', $tag);
        }

        if (!empty($countryCodes)) {
            $query = $query->withData('countrycodes', $countryCodes);
        }

        if (!empty($viewBox)) {
            $query = $query->withData('viewbox', $viewBox);
        }

        try {
            $results = $this->provider->geocodeQuery($query);
        } catch (QuotaExceeded $e) {
            throw $e;
        } catch (InvalidServerResponse|\Exception $e) {
            return [];
        }

        return $this->transformAddressCollectionToLocationsArray($results);
    }

    public function autocompleteCities(string $search, int $limit = 5): array
    {
        $tag = 'place:city,place:town,place:village';
        $countryCodes = null;
        $viewBox = '-5.4517733,41.2611155,9.8282225,51.3055721';

        return $this->autocomplete($search, $limit, $tag, $countryCodes, $viewBox);
    }

    public function autocompleteMobilities(string $search, int $limit = 5): array
    {
        $tag = 'place:country,place:city,place:town,place:state,place:village,boundary:administrative,landuse:commercial,place:suburb';
        $countryCodes = 'fr,be,ch,gb,lu,mc,ad,ca';
        $viewBox = '-5.4517733,41.2611155,9.8282225,51.3055721';

        return $this->autocomplete($search, $limit, $tag, $countryCodes, $viewBox);
    }

    protected function transformAddressCollectionToLocationsArray(Collection $addressesCollection): array
    {
        $addresses = [];
        /** @var \Iterator $it */
        $it = $addressesCollection->getIterator();

        while ($it->valid()) {
            $address = $it->current();
            $addresses[] = $this->transformAddressToLocation($address);
            $it->next();
        }

        return $addresses;
    }

    protected function transformAddressToLocation(Address $address): Location
    {
        try {
            $adminLevel1 = $address->getAdminLevels()->get(1);
            $adminLevel1Slug = mb_strtolower(Strings::slug($adminLevel1), 'utf8');
        } catch (InvalidArgument $exception) {
            $adminLevel1 = null;
            $adminLevel1Slug = null;
        }

        try {
            $adminLevel2 = $address->getAdminLevels()->get(2);
            $adminLevel2Slug = mb_strtolower(Strings::slug($adminLevel2), 'utf8');
        } catch (InvalidArgument $exception) {
            $adminLevel2 = null;
            $adminLevel2Slug = null;
        }

        $localitySlug = null;
        if (null !== $locality = $address->getLocality()) {
            $localitySlug = mb_strtolower(Strings::slug($locality), 'utf8');
        }

        $coords = $address->getCoordinates();
        $country = $address->getCountry();

        $postalCode = null;
        if (null !== $address->getPostalCode()) {
            $postalCode = Arrays::first(explode(';', $address->getPostalCode()));
        } elseif (!empty($address->getLocality())) {
            // shit fix, thanks to locationiq: lyon and paris does not have a postal code for place:city object
            if ('paris' === Strings::lower($address->getLocality())) {
                $postalCode = '75000';
            } elseif ('lyon' === Strings::lower($address->getLocality())) {
                $postalCode = '69000';
            }
        }

        $street = implode(' ', array_filter([$address->getStreetNumber(), $address->getStreetName()]));

        return (new Location())
            ->setStreet(empty($street) ? null : $street)
            ->setSubLocality($address->getSubLocality())
            ->setLocality($locality)
            ->setLocalitySlug($localitySlug)
            ->setPostalCode($postalCode)
            ->setAdminLevel1($adminLevel1)
            ->setAdminLevel1Slug($adminLevel1Slug)
            ->setAdminLevel2($adminLevel2)
            ->setAdminLevel2Slug($adminLevel2Slug)
            ->setCountryCode($country?->getCode())
            ->setCountry($country?->getName())
            ->setLatitude(null === $coords ? null : (string) $coords->getLatitude())
            ->setLongitude(null === $coords ? null : (string) $coords->getLongitude())
        ;
    }

    public function storeLocationKeyLabel(array $locations): void
    {
        $locationKeys = [];
        foreach ($locations as $location) {
            $locationKey = $location['key'];
            $locationKeyLabel = $this->em->getRepository(LocationKeyLabel::class)->findOneBy(['key' => $locationKey]);
            if (null === $locationKeyLabel) {
                $locationKeyLabel = new LocationKeyLabel($locationKey);
                $locationKeyLabel
                    ->setLabel($location['label'])
                    ->setData($location)
                ;

                if (!\in_array($locationKey, $locationKeys, true)) {
                    $this->em->persist($locationKeyLabel);
                    $locationKeys[] = $locationKey;
                }
            } else {
                $locationKeyLabel->setLabel($location['label']);
            }
        }

        $this->em->flush();
    }

    public function searchInDatabase(string $search): ?Location
    {
        return $this->freeWorkRepository->findLocation($search);
    }
}
