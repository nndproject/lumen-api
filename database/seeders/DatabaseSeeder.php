<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create('id_ID');
        for($i = 1; $i <= 200; $i++){
 
            DB::beginTransaction();
            try {
                // insert data ke table pegawai menggunakan Faker
                $user = new User();
                $user->name = $faker->name();
                $user->email = $faker->unique()->safeEmail();
                $user->email_verified_at = Carbon::now();
                $user->password = Hash::make('admin1'); 
                $user->save();
                $user->assignRole('admin');
                
                DB::commit();

            } catch (\Exception $e) {
                DB::rollback();
                // something went wrong
                echo \fLogs($e->getMessage(),'e');
                return false;
            }
      }
    }
}
