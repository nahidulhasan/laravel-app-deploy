@extends('layouts.app')

@section('content')
    <div class="container-fluid header mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
                <li class="breadcrumb-item">
                    <a href="{{route('dashboard')}}">Home</a>
                </li>
                <li class="breadcrumb-item active"><span>Compliance Entry View</span></li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <a href="{{route('compliance-entry')}}" type="button" class="btn btn-default btn-outline-dark mb-4">Back To List</a>
            <div class="card active">
                <div class="card-header">{{ __('Periodic tickets lIST') }}</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 table-responsive">
                            <table class="table table-hover">
                                <tr>
                                    <td width="250">ID</td>
                                    <td>{!! $data->id!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Created Date</td>
                                    <td><?= !empty($data->created_at) ? date('d-M-y h:i A', strtotime($data->created_at)) : ''?></td>
                                </tr>
                                <tr>
                                    <td width="250">Updated Date</td>
                                    <td><?= !empty($data->updated_at) ? date('d-M-y h:i A', strtotime($data->updated_at)) : ''?></td>
                                </tr>
                                <tr>
                                    <td width="250">Ticket Id</td>
                                    <td>{!! $data->ticket_id!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Compliance Entry No</td>
                                    <td>{!! $data->compliance_point_no!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Compliance Level</td>
                                    <td>{!! $data->compliance_level!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Compliance Category</td>
                                    <td>{!! $data->compliance_category!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Compliance Sub Category</td>
                                    <td>{!! $data->compliance_sub_category!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Compliance Point Description</td>
                                    <td>{!! $data->compliance_point_description!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Instruction Type</td>
                                    <td>{!! $data->instruction_type!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Document Subject</td>
                                    <td>{!! $data->document_subject!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Document Date</td>
                                    <td>{!! $data->document_date!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Section No</td>
                                    <td>{{$data->section_no_as_per_document}}</td>
                                </tr>

                                <tr>
                                    <td width="250">Compliance Applicable For</td>
                                    <td>{{$data->compliance_applicable_for}}</td>
                                </tr>
                                <tr>
                                    <td width="250">Start Date</td>
                                    <td>{{$data->start_date}}</td>
                                </tr>
                                <tr>
                                    <td width="250">Frequency</td>
                                    <td>{{$data->frequency}}</td>
                                </tr>
                                <tr>
                                    <td width="250">Due Date</td>
                                    <td>{{$data->due_date}}</td>
                                </tr>
                                <tr>
                                    <td width="250">Due Month</td>
                                    <td>{{$data->due_month}}</td>
                                </tr>
                                <tr>
                                    <td width="250">Next Due Date</td>
                                    <td>{{$data->next_due_date}}</td>
                                </tr>
                                <tr>
                                    <td width="250">Payment Penalty Implication Risk</td>
                                    <td>{{$data->payment_penalty_implication_risk}}</td>
                                </tr>
                                <tr>
                                    <td width="250">Compliance Owner</td>
                                    <td>{{$data->compliance_owner}}</td>
                                </tr>
                                <tr>
                                    <td width="250">Compliance Group</td>
                                    <td>{{$data->compliance_group_id}}</td>
                                </tr>
                                <tr>
                                    <td width="250">Status</td>
                                    <td>{{$data->status}}</td>
                                </tr>
                                <tr>
                                    <td width="250">Remarks</td>
                                    <td>{{$data->remarks}}</td>
                                </tr>

                            </table>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
