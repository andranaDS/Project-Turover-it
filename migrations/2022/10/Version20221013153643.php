<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Company\Entity\CompanyPicture;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

final class Version20221013153643 extends AbstractMigration implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company ADD location_street VARCHAR(255) DEFAULT NULL, ADD location_additional_data VARCHAR(255) DEFAULT NULL, ADD billing_address_street VARCHAR(255) DEFAULT NULL, ADD billing_address_sub_locality VARCHAR(255) DEFAULT NULL, ADD billing_address_locality VARCHAR(255) DEFAULT NULL, ADD billing_address_locality_slug VARCHAR(255) DEFAULT NULL, ADD billing_address_postal_code VARCHAR(255) DEFAULT NULL, ADD billing_address_admin_level1 VARCHAR(255) DEFAULT NULL, ADD billing_address_admin_level1_slug VARCHAR(255) DEFAULT NULL, ADD billing_address_admin_level2 VARCHAR(255) DEFAULT NULL, ADD billing_address_admin_level2_slug VARCHAR(255) DEFAULT NULL, ADD billing_address_country VARCHAR(255) DEFAULT NULL, ADD billing_address_country_code VARCHAR(255) DEFAULT NULL, ADD billing_address_value VARCHAR(255) DEFAULT NULL, ADD billing_address_latitude NUMERIC(11, 7) DEFAULT NULL, ADD billing_address_longitude NUMERIC(11, 7) DEFAULT NULL, ADD billing_address_additional_data VARCHAR(255) DEFAULT NULL, DROP country_code');
        $this->addSql('UPDATE company SET location_street = TRIM(CONCAT(location_street_number, " ", location_street_name)) WHERE location_street_number IS NOT NULL OR location_street_name IS NOT NULL');
        $this->addSql('ALTER TABLE company DROP location_street_number, DROP location_street_name');

        $this->addSql('ALTER TABLE job_posting ADD location_street VARCHAR(255) DEFAULT NULL, ADD location_additional_data VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE job_posting SET location_street = TRIM(CONCAT(location_street_number, " ", location_street_name)) WHERE location_street_number IS NOT NULL OR location_street_name IS NOT NULL');
        $this->addSql('ALTER TABLE job_posting DROP location_street_number, DROP location_street_name');

        $this->addSql('ALTER TABLE job_posting_search_location ADD location_street VARCHAR(255) DEFAULT NULL, ADD location_additional_data VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE job_posting_search_location SET location_street = TRIM(CONCAT(location_street_number, " ", location_street_name)) WHERE location_street_number IS NOT NULL OR location_street_name IS NOT NULL');
        $this->addSql('ALTER TABLE job_posting_search_location DROP location_street_number, DROP location_street_name');

        $this->addSql('ALTER TABLE user ADD location_street VARCHAR(255) DEFAULT NULL, ADD location_additional_data VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE user SET location_street = TRIM(CONCAT(location_street_number, " ", location_street_name)) WHERE location_street_number IS NOT NULL OR location_street_name IS NOT NULL');
        $this->addSql('ALTER TABLE user DROP location_street_number, DROP location_street_name');

        $this->addSql('ALTER TABLE user_mobility ADD location_street VARCHAR(255) DEFAULT NULL, ADD location_additional_data VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE user_mobility SET location_street = TRIM(CONCAT(location_street_number, " ", location_street_name)) WHERE location_street_number IS NOT NULL OR location_street_name IS NOT NULL');
        $this->addSql('ALTER TABLE user_mobility DROP location_street_number, DROP location_street_name');

        $this->addSql('ALTER TABLE company ADD cover_picture VARCHAR(255) DEFAULT NULL');
        $this->addSql('UPDATE company c JOIN company_picture cp ON c.cover_picture_id = cp.id SET c.cover_picture = cp.image');
        $this->addSql('DELETE cp FROM company_picture cp JOIN company c ON c.cover_picture_id = cp.id WHERE c.cover_picture_id IS NOT NULL');
        $this->addSql('ALTER TABLE company DROP FOREIGN KEY FK_4FBF094FC50D86A0');
        $this->addSql('DROP INDEX IDX_4FBF094FC50D86A0 ON company');
        $this->addSql('ALTER TABLE company DROP cover_picture_id');

        $this->addSql('ALTER TABLE company ADD quality INT DEFAULT NULL');

        $this->addSql('ALTER TABLE company_picture ADD position INT NULL');

        $this->addSql('UPDATE user SET company_country_code = "AN" WHERE company_country_code = "ANT"');
        $this->addSql('UPDATE user SET company_country_code = "CS" WHERE company_country_code = "SCG"');
        $this->addSql('UPDATE user SET company_country_code = "AF" WHERE company_country_code = "AFG"');
        $this->addSql('UPDATE user SET company_country_code = "AX" WHERE company_country_code = "ALA"');
        $this->addSql('UPDATE user SET company_country_code = "AL" WHERE company_country_code = "ALB"');
        $this->addSql('UPDATE user SET company_country_code = "DZ" WHERE company_country_code = "DZA"');
        $this->addSql('UPDATE user SET company_country_code = "AS" WHERE company_country_code = "ASM"');
        $this->addSql('UPDATE user SET company_country_code = "AD" WHERE company_country_code = "AND"');
        $this->addSql('UPDATE user SET company_country_code = "AO" WHERE company_country_code = "AGO"');
        $this->addSql('UPDATE user SET company_country_code = "AI" WHERE company_country_code = "AIA"');
        $this->addSql('UPDATE user SET company_country_code = "AQ" WHERE company_country_code = "ATA"');
        $this->addSql('UPDATE user SET company_country_code = "AG" WHERE company_country_code = "ATG"');
        $this->addSql('UPDATE user SET company_country_code = "AR" WHERE company_country_code = "ARG"');
        $this->addSql('UPDATE user SET company_country_code = "AM" WHERE company_country_code = "ARM"');
        $this->addSql('UPDATE user SET company_country_code = "AW" WHERE company_country_code = "ABW"');
        $this->addSql('UPDATE user SET company_country_code = "AU" WHERE company_country_code = "AUS"');
        $this->addSql('UPDATE user SET company_country_code = "AT" WHERE company_country_code = "AUT"');
        $this->addSql('UPDATE user SET company_country_code = "AZ" WHERE company_country_code = "AZE"');
        $this->addSql('UPDATE user SET company_country_code = "BS" WHERE company_country_code = "BHS"');
        $this->addSql('UPDATE user SET company_country_code = "BH" WHERE company_country_code = "BHR"');
        $this->addSql('UPDATE user SET company_country_code = "BD" WHERE company_country_code = "BGD"');
        $this->addSql('UPDATE user SET company_country_code = "BB" WHERE company_country_code = "BRB"');
        $this->addSql('UPDATE user SET company_country_code = "BY" WHERE company_country_code = "BLR"');
        $this->addSql('UPDATE user SET company_country_code = "BE" WHERE company_country_code = "BEL"');
        $this->addSql('UPDATE user SET company_country_code = "BZ" WHERE company_country_code = "BLZ"');
        $this->addSql('UPDATE user SET company_country_code = "BJ" WHERE company_country_code = "BEN"');
        $this->addSql('UPDATE user SET company_country_code = "BM" WHERE company_country_code = "BMU"');
        $this->addSql('UPDATE user SET company_country_code = "BT" WHERE company_country_code = "BTN"');
        $this->addSql('UPDATE user SET company_country_code = "BO" WHERE company_country_code = "BOL"');
        $this->addSql('UPDATE user SET company_country_code = "BQ" WHERE company_country_code = "BES"');
        $this->addSql('UPDATE user SET company_country_code = "BA" WHERE company_country_code = "BIH"');
        $this->addSql('UPDATE user SET company_country_code = "BW" WHERE company_country_code = "BWA"');
        $this->addSql('UPDATE user SET company_country_code = "BV" WHERE company_country_code = "BVT"');
        $this->addSql('UPDATE user SET company_country_code = "BR" WHERE company_country_code = "BRA"');
        $this->addSql('UPDATE user SET company_country_code = "IO" WHERE company_country_code = "IOT"');
        $this->addSql('UPDATE user SET company_country_code = "BN" WHERE company_country_code = "BRN"');
        $this->addSql('UPDATE user SET company_country_code = "BG" WHERE company_country_code = "BGR"');
        $this->addSql('UPDATE user SET company_country_code = "BF" WHERE company_country_code = "BFA"');
        $this->addSql('UPDATE user SET company_country_code = "BI" WHERE company_country_code = "BDI"');
        $this->addSql('UPDATE user SET company_country_code = "CV" WHERE company_country_code = "CPV"');
        $this->addSql('UPDATE user SET company_country_code = "KH" WHERE company_country_code = "KHM"');
        $this->addSql('UPDATE user SET company_country_code = "CM" WHERE company_country_code = "CMR"');
        $this->addSql('UPDATE user SET company_country_code = "CA" WHERE company_country_code = "CAN"');
        $this->addSql('UPDATE user SET company_country_code = "KY" WHERE company_country_code = "CYM"');
        $this->addSql('UPDATE user SET company_country_code = "CF" WHERE company_country_code = "CAF"');
        $this->addSql('UPDATE user SET company_country_code = "TD" WHERE company_country_code = "TCD"');
        $this->addSql('UPDATE user SET company_country_code = "CL" WHERE company_country_code = "CHL"');
        $this->addSql('UPDATE user SET company_country_code = "CN" WHERE company_country_code = "CHN"');
        $this->addSql('UPDATE user SET company_country_code = "CX" WHERE company_country_code = "CXR"');
        $this->addSql('UPDATE user SET company_country_code = "CC" WHERE company_country_code = "CCK"');
        $this->addSql('UPDATE user SET company_country_code = "CO" WHERE company_country_code = "COL"');
        $this->addSql('UPDATE user SET company_country_code = "KM" WHERE company_country_code = "COM"');
        $this->addSql('UPDATE user SET company_country_code = "CG" WHERE company_country_code = "COG"');
        $this->addSql('UPDATE user SET company_country_code = "CD" WHERE company_country_code = "COD"');
        $this->addSql('UPDATE user SET company_country_code = "CK" WHERE company_country_code = "COK"');
        $this->addSql('UPDATE user SET company_country_code = "CR" WHERE company_country_code = "CRI"');
        $this->addSql('UPDATE user SET company_country_code = "CI" WHERE company_country_code = "CIV"');
        $this->addSql('UPDATE user SET company_country_code = "HR" WHERE company_country_code = "HRV"');
        $this->addSql('UPDATE user SET company_country_code = "CU" WHERE company_country_code = "CUB"');
        $this->addSql('UPDATE user SET company_country_code = "CW" WHERE company_country_code = "CUW"');
        $this->addSql('UPDATE user SET company_country_code = "CY" WHERE company_country_code = "CYP"');
        $this->addSql('UPDATE user SET company_country_code = "CZ" WHERE company_country_code = "CZE"');
        $this->addSql('UPDATE user SET company_country_code = "DK" WHERE company_country_code = "DNK"');
        $this->addSql('UPDATE user SET company_country_code = "DJ" WHERE company_country_code = "DJI"');
        $this->addSql('UPDATE user SET company_country_code = "DM" WHERE company_country_code = "DMA"');
        $this->addSql('UPDATE user SET company_country_code = "DO" WHERE company_country_code = "DOM"');
        $this->addSql('UPDATE user SET company_country_code = "EC" WHERE company_country_code = "ECU"');
        $this->addSql('UPDATE user SET company_country_code = "EG" WHERE company_country_code = "EGY"');
        $this->addSql('UPDATE user SET company_country_code = "SV" WHERE company_country_code = "SLV"');
        $this->addSql('UPDATE user SET company_country_code = "GQ" WHERE company_country_code = "GNQ"');
        $this->addSql('UPDATE user SET company_country_code = "ER" WHERE company_country_code = "ERI"');
        $this->addSql('UPDATE user SET company_country_code = "EE" WHERE company_country_code = "EST"');
        $this->addSql('UPDATE user SET company_country_code = "SZ" WHERE company_country_code = "SWZ"');
        $this->addSql('UPDATE user SET company_country_code = "ET" WHERE company_country_code = "ETH"');
        $this->addSql('UPDATE user SET company_country_code = "FK" WHERE company_country_code = "FLK"');
        $this->addSql('UPDATE user SET company_country_code = "FO" WHERE company_country_code = "FRO"');
        $this->addSql('UPDATE user SET company_country_code = "FJ" WHERE company_country_code = "FJI"');
        $this->addSql('UPDATE user SET company_country_code = "FI" WHERE company_country_code = "FIN"');
        $this->addSql('UPDATE user SET company_country_code = "FR" WHERE company_country_code = "FRA"');
        $this->addSql('UPDATE user SET company_country_code = "GF" WHERE company_country_code = "GUF"');
        $this->addSql('UPDATE user SET company_country_code = "PF" WHERE company_country_code = "PYF"');
        $this->addSql('UPDATE user SET company_country_code = "TF" WHERE company_country_code = "ATF"');
        $this->addSql('UPDATE user SET company_country_code = "GA" WHERE company_country_code = "GAB"');
        $this->addSql('UPDATE user SET company_country_code = "GM" WHERE company_country_code = "GMB"');
        $this->addSql('UPDATE user SET company_country_code = "GE" WHERE company_country_code = "GEO"');
        $this->addSql('UPDATE user SET company_country_code = "DE" WHERE company_country_code = "DEU"');
        $this->addSql('UPDATE user SET company_country_code = "GH" WHERE company_country_code = "GHA"');
        $this->addSql('UPDATE user SET company_country_code = "GI" WHERE company_country_code = "GIB"');
        $this->addSql('UPDATE user SET company_country_code = "GR" WHERE company_country_code = "GRC"');
        $this->addSql('UPDATE user SET company_country_code = "GL" WHERE company_country_code = "GRL"');
        $this->addSql('UPDATE user SET company_country_code = "GD" WHERE company_country_code = "GRD"');
        $this->addSql('UPDATE user SET company_country_code = "GP" WHERE company_country_code = "GLP"');
        $this->addSql('UPDATE user SET company_country_code = "GU" WHERE company_country_code = "GUM"');
        $this->addSql('UPDATE user SET company_country_code = "GT" WHERE company_country_code = "GTM"');
        $this->addSql('UPDATE user SET company_country_code = "GG" WHERE company_country_code = "GGY"');
        $this->addSql('UPDATE user SET company_country_code = "GN" WHERE company_country_code = "GIN"');
        $this->addSql('UPDATE user SET company_country_code = "GW" WHERE company_country_code = "GNB"');
        $this->addSql('UPDATE user SET company_country_code = "GY" WHERE company_country_code = "GUY"');
        $this->addSql('UPDATE user SET company_country_code = "HT" WHERE company_country_code = "HTI"');
        $this->addSql('UPDATE user SET company_country_code = "HM" WHERE company_country_code = "HMD"');
        $this->addSql('UPDATE user SET company_country_code = "VA" WHERE company_country_code = "VAT"');
        $this->addSql('UPDATE user SET company_country_code = "HN" WHERE company_country_code = "HND"');
        $this->addSql('UPDATE user SET company_country_code = "HK" WHERE company_country_code = "HKG"');
        $this->addSql('UPDATE user SET company_country_code = "HU" WHERE company_country_code = "HUN"');
        $this->addSql('UPDATE user SET company_country_code = "IS" WHERE company_country_code = "ISL"');
        $this->addSql('UPDATE user SET company_country_code = "IN" WHERE company_country_code = "IND"');
        $this->addSql('UPDATE user SET company_country_code = "ID" WHERE company_country_code = "IDN"');
        $this->addSql('UPDATE user SET company_country_code = "IR" WHERE company_country_code = "IRN"');
        $this->addSql('UPDATE user SET company_country_code = "IQ" WHERE company_country_code = "IRQ"');
        $this->addSql('UPDATE user SET company_country_code = "IE" WHERE company_country_code = "IRL"');
        $this->addSql('UPDATE user SET company_country_code = "IM" WHERE company_country_code = "IMN"');
        $this->addSql('UPDATE user SET company_country_code = "IL" WHERE company_country_code = "ISR"');
        $this->addSql('UPDATE user SET company_country_code = "IT" WHERE company_country_code = "ITA"');
        $this->addSql('UPDATE user SET company_country_code = "JM" WHERE company_country_code = "JAM"');
        $this->addSql('UPDATE user SET company_country_code = "JP" WHERE company_country_code = "JPN"');
        $this->addSql('UPDATE user SET company_country_code = "JE" WHERE company_country_code = "JEY"');
        $this->addSql('UPDATE user SET company_country_code = "JO" WHERE company_country_code = "JOR"');
        $this->addSql('UPDATE user SET company_country_code = "KZ" WHERE company_country_code = "KAZ"');
        $this->addSql('UPDATE user SET company_country_code = "KE" WHERE company_country_code = "KEN"');
        $this->addSql('UPDATE user SET company_country_code = "KI" WHERE company_country_code = "KIR"');
        $this->addSql('UPDATE user SET company_country_code = "XK" WHERE company_country_code = "XXK"');
        $this->addSql('UPDATE user SET company_country_code = "KP" WHERE company_country_code = "PRK"');
        $this->addSql('UPDATE user SET company_country_code = "KR" WHERE company_country_code = "KOR"');
        $this->addSql('UPDATE user SET company_country_code = "KW" WHERE company_country_code = "KWT"');
        $this->addSql('UPDATE user SET company_country_code = "KG" WHERE company_country_code = "KGZ"');
        $this->addSql('UPDATE user SET company_country_code = "LA" WHERE company_country_code = "LAO"');
        $this->addSql('UPDATE user SET company_country_code = "LV" WHERE company_country_code = "LVA"');
        $this->addSql('UPDATE user SET company_country_code = "LB" WHERE company_country_code = "LBN"');
        $this->addSql('UPDATE user SET company_country_code = "LS" WHERE company_country_code = "LSO"');
        $this->addSql('UPDATE user SET company_country_code = "LR" WHERE company_country_code = "LBR"');
        $this->addSql('UPDATE user SET company_country_code = "LY" WHERE company_country_code = "LBY"');
        $this->addSql('UPDATE user SET company_country_code = "LI" WHERE company_country_code = "LIE"');
        $this->addSql('UPDATE user SET company_country_code = "LT" WHERE company_country_code = "LTU"');
        $this->addSql('UPDATE user SET company_country_code = "LU" WHERE company_country_code = "LUX"');
        $this->addSql('UPDATE user SET company_country_code = "MO" WHERE company_country_code = "MAC"');
        $this->addSql('UPDATE user SET company_country_code = "MG" WHERE company_country_code = "MDG"');
        $this->addSql('UPDATE user SET company_country_code = "MW" WHERE company_country_code = "MWI"');
        $this->addSql('UPDATE user SET company_country_code = "MY" WHERE company_country_code = "MYS"');
        $this->addSql('UPDATE user SET company_country_code = "MV" WHERE company_country_code = "MDV"');
        $this->addSql('UPDATE user SET company_country_code = "ML" WHERE company_country_code = "MLI"');
        $this->addSql('UPDATE user SET company_country_code = "MT" WHERE company_country_code = "MLT"');
        $this->addSql('UPDATE user SET company_country_code = "MH" WHERE company_country_code = "MHL"');
        $this->addSql('UPDATE user SET company_country_code = "MQ" WHERE company_country_code = "MTQ"');
        $this->addSql('UPDATE user SET company_country_code = "MR" WHERE company_country_code = "MRT"');
        $this->addSql('UPDATE user SET company_country_code = "MU" WHERE company_country_code = "MUS"');
        $this->addSql('UPDATE user SET company_country_code = "YT" WHERE company_country_code = "MYT"');
        $this->addSql('UPDATE user SET company_country_code = "MX" WHERE company_country_code = "MEX"');
        $this->addSql('UPDATE user SET company_country_code = "FM" WHERE company_country_code = "FSM"');
        $this->addSql('UPDATE user SET company_country_code = "MD" WHERE company_country_code = "MDA"');
        $this->addSql('UPDATE user SET company_country_code = "MC" WHERE company_country_code = "MCO"');
        $this->addSql('UPDATE user SET company_country_code = "MN" WHERE company_country_code = "MNG"');
        $this->addSql('UPDATE user SET company_country_code = "ME" WHERE company_country_code = "MNE"');
        $this->addSql('UPDATE user SET company_country_code = "MS" WHERE company_country_code = "MSR"');
        $this->addSql('UPDATE user SET company_country_code = "MA" WHERE company_country_code = "MAR"');
        $this->addSql('UPDATE user SET company_country_code = "MZ" WHERE company_country_code = "MOZ"');
        $this->addSql('UPDATE user SET company_country_code = "MM" WHERE company_country_code = "MMR"');
        $this->addSql('UPDATE user SET company_country_code = "NA" WHERE company_country_code = "NAM"');
        $this->addSql('UPDATE user SET company_country_code = "NR" WHERE company_country_code = "NRU"');
        $this->addSql('UPDATE user SET company_country_code = "NP" WHERE company_country_code = "NPL"');
        $this->addSql('UPDATE user SET company_country_code = "NL" WHERE company_country_code = "NLD"');
        $this->addSql('UPDATE user SET company_country_code = "NC" WHERE company_country_code = "NCL"');
        $this->addSql('UPDATE user SET company_country_code = "NZ" WHERE company_country_code = "NZL"');
        $this->addSql('UPDATE user SET company_country_code = "NI" WHERE company_country_code = "NIC"');
        $this->addSql('UPDATE user SET company_country_code = "NE" WHERE company_country_code = "NER"');
        $this->addSql('UPDATE user SET company_country_code = "NG" WHERE company_country_code = "NGA"');
        $this->addSql('UPDATE user SET company_country_code = "NU" WHERE company_country_code = "NIU"');
        $this->addSql('UPDATE user SET company_country_code = "NF" WHERE company_country_code = "NFK"');
        $this->addSql('UPDATE user SET company_country_code = "MK" WHERE company_country_code = "MKD"');
        $this->addSql('UPDATE user SET company_country_code = "MP" WHERE company_country_code = "MNP"');
        $this->addSql('UPDATE user SET company_country_code = "NO" WHERE company_country_code = "NOR"');
        $this->addSql('UPDATE user SET company_country_code = "OM" WHERE company_country_code = "OMN"');
        $this->addSql('UPDATE user SET company_country_code = "PK" WHERE company_country_code = "PAK"');
        $this->addSql('UPDATE user SET company_country_code = "PW" WHERE company_country_code = "PLW"');
        $this->addSql('UPDATE user SET company_country_code = "PS" WHERE company_country_code = "PSE"');
        $this->addSql('UPDATE user SET company_country_code = "PA" WHERE company_country_code = "PAN"');
        $this->addSql('UPDATE user SET company_country_code = "PG" WHERE company_country_code = "PNG"');
        $this->addSql('UPDATE user SET company_country_code = "PY" WHERE company_country_code = "PRY"');
        $this->addSql('UPDATE user SET company_country_code = "PE" WHERE company_country_code = "PER"');
        $this->addSql('UPDATE user SET company_country_code = "PH" WHERE company_country_code = "PHL"');
        $this->addSql('UPDATE user SET company_country_code = "PN" WHERE company_country_code = "PCN"');
        $this->addSql('UPDATE user SET company_country_code = "PL" WHERE company_country_code = "POL"');
        $this->addSql('UPDATE user SET company_country_code = "PT" WHERE company_country_code = "PRT"');
        $this->addSql('UPDATE user SET company_country_code = "PR" WHERE company_country_code = "PRI"');
        $this->addSql('UPDATE user SET company_country_code = "QA" WHERE company_country_code = "QAT"');
        $this->addSql('UPDATE user SET company_country_code = "RE" WHERE company_country_code = "REU"');
        $this->addSql('UPDATE user SET company_country_code = "RO" WHERE company_country_code = "ROU"');
        $this->addSql('UPDATE user SET company_country_code = "RU" WHERE company_country_code = "RUS"');
        $this->addSql('UPDATE user SET company_country_code = "RW" WHERE company_country_code = "RWA"');
        $this->addSql('UPDATE user SET company_country_code = "BL" WHERE company_country_code = "BLM"');
        $this->addSql('UPDATE user SET company_country_code = "SH" WHERE company_country_code = "SHN"');
        $this->addSql('UPDATE user SET company_country_code = "KN" WHERE company_country_code = "KNA"');
        $this->addSql('UPDATE user SET company_country_code = "LC" WHERE company_country_code = "LCA"');
        $this->addSql('UPDATE user SET company_country_code = "MF" WHERE company_country_code = "MAF"');
        $this->addSql('UPDATE user SET company_country_code = "PM" WHERE company_country_code = "SPM"');
        $this->addSql('UPDATE user SET company_country_code = "VC" WHERE company_country_code = "VCT"');
        $this->addSql('UPDATE user SET company_country_code = "WS" WHERE company_country_code = "WSM"');
        $this->addSql('UPDATE user SET company_country_code = "SM" WHERE company_country_code = "SMR"');
        $this->addSql('UPDATE user SET company_country_code = "ST" WHERE company_country_code = "STP"');
        $this->addSql('UPDATE user SET company_country_code = "SA" WHERE company_country_code = "SAU"');
        $this->addSql('UPDATE user SET company_country_code = "SN" WHERE company_country_code = "SEN"');
        $this->addSql('UPDATE user SET company_country_code = "RS" WHERE company_country_code = "SRB"');
        $this->addSql('UPDATE user SET company_country_code = "SC" WHERE company_country_code = "SYC"');
        $this->addSql('UPDATE user SET company_country_code = "SL" WHERE company_country_code = "SLE"');
        $this->addSql('UPDATE user SET company_country_code = "SG" WHERE company_country_code = "SGP"');
        $this->addSql('UPDATE user SET company_country_code = "SX" WHERE company_country_code = "SXM"');
        $this->addSql('UPDATE user SET company_country_code = "SK" WHERE company_country_code = "SVK"');
        $this->addSql('UPDATE user SET company_country_code = "SI" WHERE company_country_code = "SVN"');
        $this->addSql('UPDATE user SET company_country_code = "SB" WHERE company_country_code = "SLB"');
        $this->addSql('UPDATE user SET company_country_code = "SO" WHERE company_country_code = "SOM"');
        $this->addSql('UPDATE user SET company_country_code = "ZA" WHERE company_country_code = "ZAF"');
        $this->addSql('UPDATE user SET company_country_code = "GS" WHERE company_country_code = "SGS"');
        $this->addSql('UPDATE user SET company_country_code = "SS" WHERE company_country_code = "SSD"');
        $this->addSql('UPDATE user SET company_country_code = "ES" WHERE company_country_code = "ESP"');
        $this->addSql('UPDATE user SET company_country_code = "LK" WHERE company_country_code = "LKA"');
        $this->addSql('UPDATE user SET company_country_code = "SD" WHERE company_country_code = "SDN"');
        $this->addSql('UPDATE user SET company_country_code = "SR" WHERE company_country_code = "SUR"');
        $this->addSql('UPDATE user SET company_country_code = "SJ" WHERE company_country_code = "SJM"');
        $this->addSql('UPDATE user SET company_country_code = "SE" WHERE company_country_code = "SWE"');
        $this->addSql('UPDATE user SET company_country_code = "CH" WHERE company_country_code = "CHE"');
        $this->addSql('UPDATE user SET company_country_code = "SY" WHERE company_country_code = "SYR"');
        $this->addSql('UPDATE user SET company_country_code = "TW" WHERE company_country_code = "TWN"');
        $this->addSql('UPDATE user SET company_country_code = "TJ" WHERE company_country_code = "TJK"');
        $this->addSql('UPDATE user SET company_country_code = "TZ" WHERE company_country_code = "TZA"');
        $this->addSql('UPDATE user SET company_country_code = "TH" WHERE company_country_code = "THA"');
        $this->addSql('UPDATE user SET company_country_code = "TL" WHERE company_country_code = "TLS"');
        $this->addSql('UPDATE user SET company_country_code = "TG" WHERE company_country_code = "TGO"');
        $this->addSql('UPDATE user SET company_country_code = "TK" WHERE company_country_code = "TKL"');
        $this->addSql('UPDATE user SET company_country_code = "TO" WHERE company_country_code = "TON"');
        $this->addSql('UPDATE user SET company_country_code = "TT" WHERE company_country_code = "TTO"');
        $this->addSql('UPDATE user SET company_country_code = "TN" WHERE company_country_code = "TUN"');
        $this->addSql('UPDATE user SET company_country_code = "TR" WHERE company_country_code = "TUR"');
        $this->addSql('UPDATE user SET company_country_code = "TM" WHERE company_country_code = "TKM"');
        $this->addSql('UPDATE user SET company_country_code = "TC" WHERE company_country_code = "TCA"');
        $this->addSql('UPDATE user SET company_country_code = "TV" WHERE company_country_code = "TUV"');
        $this->addSql('UPDATE user SET company_country_code = "UG" WHERE company_country_code = "UGA"');
        $this->addSql('UPDATE user SET company_country_code = "UA" WHERE company_country_code = "UKR"');
        $this->addSql('UPDATE user SET company_country_code = "AE" WHERE company_country_code = "ARE"');
        $this->addSql('UPDATE user SET company_country_code = "GB" WHERE company_country_code = "GBR"');
        $this->addSql('UPDATE user SET company_country_code = "US" WHERE company_country_code = "USA"');
        $this->addSql('UPDATE user SET company_country_code = "UM" WHERE company_country_code = "UMI"');
        $this->addSql('UPDATE user SET company_country_code = "UY" WHERE company_country_code = "URY"');
        $this->addSql('UPDATE user SET company_country_code = "UZ" WHERE company_country_code = "UZB"');
        $this->addSql('UPDATE user SET company_country_code = "VU" WHERE company_country_code = "VUT"');
        $this->addSql('UPDATE user SET company_country_code = "VE" WHERE company_country_code = "VEN"');
        $this->addSql('UPDATE user SET company_country_code = "VN" WHERE company_country_code = "VNM"');
        $this->addSql('UPDATE user SET company_country_code = "VG" WHERE company_country_code = "VGB"');
        $this->addSql('UPDATE user SET company_country_code = "VI" WHERE company_country_code = "VIR"');
        $this->addSql('UPDATE user SET company_country_code = "WF" WHERE company_country_code = "WLF"');
        $this->addSql('UPDATE user SET company_country_code = "EH" WHERE company_country_code = "ESH"');
        $this->addSql('UPDATE user SET company_country_code = "YE" WHERE company_country_code = "YEM"');
        $this->addSql('UPDATE user SET company_country_code = "ZM" WHERE company_country_code = "ZMB"');
        $this->addSql('UPDATE user SET company_country_code = "ZW" WHERE company_country_code = "ZWE"');
        $this->addSql('UPDATE user SET company_country_code = "OO" WHERE company_country_code = "OOO"');

        $this->addSql('ALTER TABLE job_posting_template ADD location_street VARCHAR(255) DEFAULT NULL, ADD location_additional_data VARCHAR(255) DEFAULT NULL, DROP location_street_number, DROP location_street_name');
    }

    public function postUp(Schema $schema): void
    {
        $companies = [];
        $em = $this->container->get('doctrine.orm.entity_manager');
        $companyPicutres = $em->getRepository(CompanyPicture::class)->findBy([], ['id' => 'ASC']);

        foreach ($companyPicutres as $companyPicture) {
            $companies[$companyPicture->getCompany()->getId()][] = $companyPicture;
        }

        foreach ($companies as $companyPicturesSorted) {
            $i = 0;
            foreach ($companyPicturesSorted as $companyPicutre) {
                $companyPicutre->setPosition($i);
                ++$i;
            }
        }

        $em->flush();

        $em->getConnection()->executeStatement('ALTER TABLE company_picture CHANGE position position INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE company ADD location_street_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_street_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD country_code VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP location_street, DROP location_additional_data, DROP billing_address_street, DROP billing_address_sub_locality, DROP billing_address_locality, DROP billing_address_locality_slug, DROP billing_address_postal_code, DROP billing_address_admin_level1, DROP billing_address_admin_level1_slug, DROP billing_address_admin_level2, DROP billing_address_admin_level2_slug, DROP billing_address_country, DROP billing_address_country_code, DROP billing_address_value, DROP billing_address_latitude, DROP billing_address_longitude, DROP billing_address_additional_data');
        $this->addSql('ALTER TABLE job_posting ADD location_street_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_street_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP location_street, DROP location_additional_data');
        $this->addSql('ALTER TABLE job_posting_search_location ADD location_street_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_street_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP location_street, DROP location_additional_data');
        $this->addSql('ALTER TABLE user ADD location_street_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_street_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP location_street, DROP location_additional_data');
        $this->addSql('ALTER TABLE user_mobility ADD location_street_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_street_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP location_street, DROP location_additional_data');
        $this->addSql('ALTER TABLE company ADD cover_picture_id INT DEFAULT NULL, DROP cover_picture');
        $this->addSql('ALTER TABLE company ADD CONSTRAINT FK_4FBF094FC50D86A0 FOREIGN KEY (cover_picture_id) REFERENCES company_picture (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_4FBF094FC50D86A0 ON company (cover_picture_id)');
        $this->addSql('ALTER TABLE company DROP quality');
        $this->addSql('ALTER TABLE company_picture DROP position');

        $this->addSql('ALTER TABLE job_posting_template ADD location_street_number VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD location_street_name VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP location_street, DROP location_additional_data');
    }
}
