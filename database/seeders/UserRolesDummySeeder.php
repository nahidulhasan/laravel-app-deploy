<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRolesDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_roles')->insert(
            [
                [
                    'compliance_owner' => 'rcms_execute1@yopmail.com',
                    'line_manager' => 'rcms_supervisor1@yopmail.com',
                    'emt' => 'rcms_emt1@yopmail.com',
                    'cxo' => 'rcms_cxo1@yopmail.com',
                    'ceo' => 'rcms_ceo@yopmail.com',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'rcms_execute2@yopmail.com',
                    'line_manager' => 'rcms_supervisor1@yopmail.com',
                    'emt' => 'rcms_emt1@yopmail.com',
                    'cxo' => 'rcms_cxo2@yopmail.com',
                    'ceo' => 'rcms_ceo@yopmail.com',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'rcms_execute3@yopmail.com',
                    'line_manager' => 'rcms_supervisor2@yopmail.com',
                    'emt' => 'rcms_emt2@yopmail.com',
                    'cxo' => 'rcms_cxo1@yopmail.com',
                    'ceo' => 'rcms_ceo@yopmail.com',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
            ]
        );
    }
}
