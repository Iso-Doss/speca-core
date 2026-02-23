<?php

namespace Speca\SpecaCore\Database\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Speca\SpecaCore\Models\Country;
use Speca\SpecaCore\Models\User;
use Speca\SpecaCore\Models\UserProfile;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $users = [
            // Top management.
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'yerima@paydunya.com',
                'full_name' => 'Aziz YERIMA',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => ['administrator'],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'fall@paydunya.com',
                'full_name' => 'Youma Fall',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],

            // Finops team.
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'zoul.yerima@paydunya.com',
                'full_name' => 'Zoul YERIMA',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'fbarry@paydunya.com ',
                'full_name' => 'Fatoumata Binta BARRY',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'ivan.adegbindin@paydunya.com',
                'full_name' => 'Ivan Raphaël ADEGBINDIN',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'ramatoulaye.diop@paydunya.com',
                'full_name' => 'Ramatoulaye DIOP',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],

            // Risk team.
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'palouki@paydunya.com',
                'full_name' => 'Christian PALOUKI',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'camille.alladjim@paydunya.com',
                'full_name' => 'Camille ALLADJIM',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'rama.bio@paydunya.com',
                'full_name' => 'BIO SEKO Rahamatou',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'oumar.seck@paydunya.com',
                'full_name' => 'Oumar MARAM GANE SECK',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'dieudonne.aikpe@paydunya.com',
                'full_name' => 'Marie Ange Leslie Dieudonné AIKPE',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],

            // Technical Team.
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'israel.dossou@paydunya.com',
                'full_name' => 'Israel DOSSOU',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'djibril.ba@paydunya.com',
                'full_name' => 'Djibril BA',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'christian.awassi@paydunya.com',
                'full_name' => 'Christian AWASSI',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'onesine.lewhe@paydunya.com',
                'full_name' => 'Onésine LEWHE',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'tanguy.nobime@paydunya.com',
                'full_name' => 'NOBIME Tanguy Adonis ',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'rick.nvenuto@paydunya.com',
                'full_name' => 'Rick DEGAN',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'cheikh.dieng@paydunya.com',
                'full_name' => 'Cheikh IBRAHIMA DIENG',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],

            // Sales Team
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'samba.mbaye@paydunya.com',
                'full_name' => 'Pape Samba Saly Mbaye',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            [
                'user_profile_id' => UserProfile::where('name', 'Collaborateur interne')->first()?->id,
                'email' => 'isaac.djidenou@paydunya.com',
                'full_name' => 'Isaac DJIDENOU',
                'password' => Hash::make('Password@' . now()->year),
                'phone_with_indicative' => NULL,
                'country_id' => Country::where('code', 'SN')->first()?->id,
                'roles' => [],
            ],
            //[
            //    'user_profile_id' => UserProfile::where('name','Collaborateur interne')->first()?->id,
            //    'email' => '',
            //    'full_name' => '',
            //    'password' => Hash::make('Password@' . now()->year),
            //    'phone_with_indicative' => NULL,
            //    'country_id' => Country::where('code', 'SN')->first()?->id,
            //    'roles' => [],
            //],
            //[
            //    'user_profile_id' => UserProfile::where('name','Collaborateur interne')->first()?->id,
            //    'email' => '',
            //    'full_name' => '',
            //    'password' => Hash::make('Password@' . now()->year),
            //    'phone_with_indicative' => NULL,
            //    'country_id' => Country::where('code', 'SN')->first()?->id,
            //    'roles' => [],
            //],
            //[
            //    'user_profile_id' => UserProfile::where('name','Collaborateur interne')->first()?->id,
            //    'email' => '',
            //    'full_name' => '',
            //    'password' => Hash::make('Password@' . now()->year),
            //    'phone_with_indicative' => NULL,
            //    'country_id' => Country::where('code', 'SN')->first()?->id,
            //    'roles' => [],
            //],
            //[
            //    'user_profile_id' => UserProfile::where('name','Collaborateur interne')->first()?->id,
            //    'email' => '',
            //    'full_name' => '',
            //    'password' => Hash::make('Password@' . now()->year),
            //    'phone_with_indicative' => NULL,
            //    'country_id' => Country::where('code', 'SN')->first()?->id,
            //    'roles' => [],
            //],
        ];

        foreach ($users as $user) {
            $roles = $user['roles'];
            unset($user['roles']);
            $user['phone_with_indicative'] = !empty($user['phone_with_indicative']) ? $user['phone_with_indicative'] : NULL;
            $user = User::withTrashed()->updateOrCreate(['email' => $user['email'], 'phone_with_indicative' => $user['phone_with_indicative']], $user);
            if ($user) {
                $user->syncRoles($roles);
            }
        }
    }
}
