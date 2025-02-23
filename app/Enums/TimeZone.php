<?php

namespace App\Enums;

enum TimeZone: string
{
    case UTC = 'UTC';
    case AFRICA_ABIDJAN = 'Africa/Abidjan';
    case AFRICA_ACCRA = 'Africa/Accra';
    case AFRICA_ADDIS_ABABA = 'Africa/Addis_Ababa';
    case AFRICA_ALGIERS = 'Africa/Algiers';
    case AFRICA_ASMARA = 'Africa/Asmara';
    case AFRICA_BAMAKO = 'Africa/Bamako';
    case AFRICA_BANGUI = 'Africa/Bangui';
    case AFRICA_BANJUL = 'Africa/Banjul';
    case AFRICA_BISSAU = 'Africa/Bissau';
    case AFRICA_BLANTYRE = 'Africa/Blantyre';
    case AFRICA_BRAZZAVILLE = 'Africa/Brazzaville';
    case AFRICA_BUJUMBURA = 'Africa/Bujumbura';
    case AFRICA_CAIRO = 'Africa/Cairo';
    case AFRICA_CASABLANCA = 'Africa/Casablanca';
    case AFRICA_CEUTA = 'Africa/Ceuta';
    case AFRICA_CONAKRY = 'Africa/Conakry';
    case AFRICA_DAKAR = 'Africa/Dakar';
    case AFRICA_DAR_ES_SALAAM = 'Africa/Dar_es_Salaam';
    case AFRICA_DJIBOUTI = 'Africa/Djibouti';
    case AFRICA_DOUALA = 'Africa/Douala';
    case AFRICA_EL_AAIUN = 'Africa/El_Aaiun';
    case AFRICA_FREETOWN = 'Africa/Freetown';
    case AFRICA_GABORONE = 'Africa/Gaborone';
    case AFRICA_HARARE = 'Africa/Harare';
    case AFRICA_JOHANNESBURG = 'Africa/Johannesburg';
    case AFRICA_JUBA = 'Africa/Juba';
    case AFRICA_KAMPALA = 'Africa/Kampala';
    case AFRICA_KHARTOUM = 'Africa/Khartoum';
    case AFRICA_KIGALI = 'Africa/Kigali';
    case AFRICA_KINSHASA = 'Africa/Kinshasa';
    case AFRICA_LAGOS = 'Africa/Lagos';

    public function label(): string
    {
        return match($this) {
            self::UTC => 'UTC',
            self::AFRICA_ABIDJAN => 'Abidjan',
            self::AFRICA_ACCRA => 'Accra',
            self::AFRICA_ADDIS_ABABA => 'Addis Ababa',
            self::AFRICA_ALGIERS => 'Algiers',
            self::AFRICA_ASMARA => 'Asmara',
            self::AFRICA_BAMAKO => 'Bamako',
            self::AFRICA_BANGUI => 'Bangui',
            self::AFRICA_BANJUL => 'Banjul',
            self::AFRICA_BISSAU => 'Bissau',
            self::AFRICA_BLANTYRE => 'Blantyre',
            self::AFRICA_BRAZZAVILLE => 'Brazzaville',
            self::AFRICA_BUJUMBURA => 'Bujumbura',
            self::AFRICA_CAIRO => 'Cairo',
            self::AFRICA_CASABLANCA => 'Casablanca',
            self::AFRICA_CEUTA => 'Ceuta',
            self::AFRICA_CONAKRY => 'Conakry',
            self::AFRICA_DAKAR => 'Dakar',
            self::AFRICA_DAR_ES_SALAAM => 'Dar es Salaam',
            self::AFRICA_DJIBOUTI => 'Djibouti',
            self::AFRICA_DOUALA => 'Douala',
            self::AFRICA_EL_AAIUN => 'El Aaiun',
            self::AFRICA_FREETOWN => 'Freetown',
            self::AFRICA_GABORONE => 'Gaborone',
            self::AFRICA_HARARE => 'Harare',
            self::AFRICA_JOHANNESBURG => 'Johannesburg',
            self::AFRICA_JUBA => 'Juba',
            self::AFRICA_KAMPALA => 'Kampala',
            self::AFRICA_KHARTOUM => 'Khartoum',
            self::AFRICA_KIGALI => 'Kigali',
            self::AFRICA_KINSHASA => 'Kinshasa',
            self::AFRICA_LAGOS => 'Lagos',
        };
    }
}
