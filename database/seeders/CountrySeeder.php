<?php

namespace Speca\SpecaCore\Database\Database\Seeders;

use Illuminate\Database\Seeder;
use Speca\SpecaCore\Models\Country;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            [
                'name' => 'Bénin',
                'code' => 'BJ',
                'iso_code' => 'BEN',
                'phone_code' => '+229',
                'flag' => '',
                'default_currency' => 'XOF',
            ],
            [
                'name' => 'Sénégal',
                'code' => 'SN',
                'iso_code' => 'SEN',
                'phone_code' => '+221',
                'flag' => '',
                'default_currency' => 'XOF',
            ],
            [
                'name' => 'Togo',
                'code' => 'TG',
                'iso_code' => 'TGO',
                'phone_code' => '+228',
                'flag' => '',
                'default_currency' => 'XOF',
            ],
            [
                'name' => 'Cote d\'Ivoire',
                'code' => 'CI',
                'iso_code' => 'CIV',
                'phone_code' => '+225',
                'flag' => '',
                'default_currency' => 'XOF',
            ],
            [
                'name' => 'Mali',
                'code' => 'ML',
                'iso_code' => 'MLI',
                'phone_code' => '+223',
                'flag' => '',
                'default_currency' => 'XOF',
            ],
            [
                'name' => 'Burkina Faso',
                'code' => 'BF',
                'iso_code' => 'BFA',
                'phone_code' => '+226',
                'flag' => '',
                'default_currency' => 'XOF',
            ],
            [
                'name' => 'Cameroun',
                'code' => 'CM',
                'iso_code' => 'CMR',
                'phone_code' => '+237',
                'flag' => '',
                'default_currency' => 'XAF',
            ],
        ];

        foreach ($countries as $country) {
            Country::withTrashed()->updateOrCreate(['name' => $country['name']], $country);
        }
    }
}
