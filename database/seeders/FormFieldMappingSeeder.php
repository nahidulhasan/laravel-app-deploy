<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormFieldMappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('form_field_mappings')->truncate();
        DB::table('form_field_mappings')->insert(
            [
                [
                    'form_field_id' => 'TicketId',
                    'compliance_entry_table_column_reference' => 'ticket_id',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'CompliancePointNo',
                    'compliance_entry_table_column_reference' => 'compliance_point_no',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'ComplianceLevel',
                    'compliance_entry_table_column_reference' => 'compliance_level',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'RegulatoryBody',
                    'compliance_entry_table_column_reference' => 'regulatory_body',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'ComplianceCategory',
                    'compliance_entry_table_column_reference' => 'compliance_category',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'ComplianceSubCategory',
                    'compliance_entry_table_column_reference' => 'compliance_sub_category',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'CompliancePointDescription',
                    'compliance_entry_table_column_reference' => 'compliance_point_description',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'InstructionType',
                    'compliance_entry_table_column_reference' => 'instruction_type',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'DocumentSubject',
                    'compliance_entry_table_column_reference' => 'document_subject',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'DocumentDate',
                    'compliance_entry_table_column_reference' => 'document_date',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'SectionNoAsPerDocument',
                    'compliance_entry_table_column_reference' => 'section_no_as_per_document',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'ComplianceApplicableFor',
                    'compliance_entry_table_column_reference' => 'compliance_applicable_for',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'Frequency',
                    'compliance_entry_table_column_reference' => 'frequency',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'DueDate',
                    'compliance_entry_table_column_reference' => 'due_date',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'DueMonth',
                    'compliance_entry_table_column_reference' => 'due_month',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'NextDueDate',
                    'compliance_entry_table_column_reference' => 'next_due_date',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'PaymentPenaltyImplicationRisk',
                    'compliance_entry_table_column_reference' => 'payment_penalty_implication_risk',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'ComplianceOwner',
                    'compliance_entry_table_column_reference' => 'compliance_owner',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'Remarks',
                    'compliance_entry_table_column_reference' => 'remarks',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
                [
                    'form_field_id' => 'Status',
                    'compliance_entry_table_column_reference' => 'status',
                    'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                    'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
                ],
            ]
        );
    }
}
