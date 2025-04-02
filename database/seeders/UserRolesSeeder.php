<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tableName = 'user_roles';
        DB::table($tableName)->truncate();
        DB::table($tableName)->insert(
            [
                [
                    'compliance_owner' => 'atahar@grameenphone.com',
                    'line_manager' => 'arifuddin@grameenphone.com',
                    'emt' => 'arifuddin@grameenphone.com',
                    'cxo' => 'jens.becker@grameenphone.com',
                    'ceo' => 'yasir.azman@grameenphone.com',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'mohsin.m@grameenphone.com',
                    'line_manager' => 'arifuddin@grameenphone.com',
                    'emt' => 'arifuddin@grameenphone.com',
                    'cxo' => 'jens.becker@grameenphone.com',
                    'ceo' => 'yasir.azman@grameenphone.com',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'mr.rahman@grameenphone.com',
                    'line_manager' => 'mohsin.m@grameenphone.com',
                    'emt' => 'arifuddin@grameenphone.com',
                    'cxo' => 'jens.becker@grameenphone.com',
                    'ceo' => 'yasir.azman@grameenphone.com',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'shsikder@grameenphone.com',
                    'line_manager' => 'rezwan.rafique@grameenphone.com',
                    'emt' => 'rezwan.rafique@grameenphone.com',
                    'cxo' => 'jens.becker@grameenphone.com',
                    'ceo' => 'yasir.azman@grameenphone.com',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'm_jahidul@grameenphone.com',
                    'line_manager' => 'rezwan.rafique@grameenphone.com',
                    'emt' => 'arifuddin@grameenphone.com',
                    'cxo' => 'jens.becker@grameenphone.com',
                    'ceo' => 'yasir.azman@grameenphone.com',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'iakhan@grameenphone.com',
                    'line_manager' => 'Sadat@grameenphone.com',
                    'emt' => 'Sadat@grameenphone.com',
                    'cxo' => 'hans.martin@grameenphone.com',
                    'ceo' => 'yasir.azman@grameenphone.com',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],

                [
                    'compliance_owner' => 'aulad@grameenphone.com',
                    'line_manager' => 's_hasib@grameenphone.com',
                    'emt' => null,
                    'cxo' => 'js_hasib@grameenphone.com',
                    'ceo' => 'yasir.azman@grameenphone.com',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'rasheda@grameenphone.com',
                    'line_manager' => 'solaiman.alam@grameenphone.com',
                    'emt' => null,
                    'cxo' => 'solaiman.alam@grameenphone.com',
                    'ceo' => 'yasir.azman@grameenphone.com',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'sjahan@grameenphone.com',
                    'line_manager' => 'alamin@grameenphone.com',
                    'emt' => 'alamin@grameenphone.com',
                    'cxo' => 'jai.prakash@grameenphone.com',
                    'ceo' => 'yasir.azman@grameenphone.com',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'Smhaque@grameenphone.com',
                    'line_manager' => 'jai.prakash@grameenphone.com',
                    'emt' => null,
                    'cxo' => 'jai.prakash@grameenphone.com',
                    'ceo' => 'yasir.azman@grameenphone.com',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 's_hasib@grameenphone.com',
                    'line_manager' => 'yasir.azman@grameenphone.com',
                    'emt' => null,
                    'cxo' => 's_hasib@grameenphone.com',
                    'ceo' => 'yasir.azman@grameenphone.com',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                // new sheet april 16th 
                // ca
                [
                    'compliance_owner' => 'iakhan@grameenphone.com',
                    'line_manager' => 'Animesh.chakrabarty@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                // cosec
                [
                    'compliance_owner' => 'rakib.rahman@grameenphone.com',
                    'line_manager' => 'Animesh.chakrabarty@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                //digital
                [
                    'compliance_owner' => 'prince@grameenphone.com',
                    'line_manager' => 'Animesh.chakrabarty@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                // business
                [
                    'compliance_owner' => 'sanjana.shammi@grameenphone.com',
                    'line_manager' => 'Animesh.chakrabarty@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'nayan@grameenphone.com',
                    'line_manager' => 'Animesh.chakrabarty@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                // p&o
                [
                    'compliance_owner' => 'nadia.ishaq@grameenphone.com',
                    'line_manager' => 'Animesh.chakrabarty@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                // commercial
                [
                    'compliance_owner' => 'masudul_islam@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],

                [
                    'compliance_owner' => 'obydur@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],

                [
                    'compliance_owner' => 'salahuddin@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],

                [
                    'compliance_owner' => 'shamsad@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],

                [
                    'compliance_owner' => 'sharif.tushar@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                //technology
                [
                    'compliance_owner' => 'afzal@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'ashfaqur@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'israt.iqbal@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'kaushik.ahmed@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'mehedi.mhasan@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'muhibur@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'nurun.nahar@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'tanveer_haque@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'm.tamim.hossain@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                // finance
                [
                    'compliance_owner' => 'mohsin.m@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'mr.rahman@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'faruq.hossen@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'kahasnat@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'mmasudur@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'fariza@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'nusrat.adib@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'benazir@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'mahbub.zaman@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'saifullah@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'compliance_owner' => 'mashiur@grameenphone.com',
                    'line_manager' => 'tikhan@grameenphone.com',
                    'emt' => 'Nadia.zaman@grameenphone.com',
                    'cxo' => null,
                    'ceo' => null,
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ]
            ]
        );
    }
}
