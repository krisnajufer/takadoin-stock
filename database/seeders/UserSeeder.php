<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = file_get_contents('database/seeders/json/user.json');
        $users = json_decode($users);
        foreach ($users as $key => $row) {
            DB::beginTransaction();
            try {
                $user = new User();
                $user->id = User::get_new_code();
                $user->username = $row->username;
                $user->email = $row->email;
                $user->role = $row->role;
                $user->password = bcrypt($row->password);
                $user->save();
                DB::commit();
            } catch (\Exception $ex) {
                //throw $th;
                echo $ex->getMessage();
                DB::rollBack();
            }
        }
    }
}
