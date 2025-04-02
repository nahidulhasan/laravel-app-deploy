@extends('layouts.app')

@section('content')
    <link href="{{asset('css/jquery.dataTables.css')}}" rel="stylesheet">
    <div class="container-fluid header mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
                <li class="breadcrumb-item">
                    <a href="{{route('dashboard')}}">Home</a>
                </li>
                <li class="breadcrumb-item active"><span>Compliance Entry</span></li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card active">
                <div class="card-header">{{ __('Compliance entry list') }}
                    <div class="card-tools">
                        <div class="row">
                            <div class="col-md-12">
                                <div style="float: right">
                                    <form action="" method="GET">
                                        <div class="input-group input-group-sm" style="width: 250px;">
                                            <input name="search" value="{{ request('search') }}"  type="text" class="form-control float-right" placeholder="Search">
                                            <div class="input-group-append">
                                                <button style="color: white; margin-left: 5px" type="submit" class="btn btn-info">
                                                    Submit
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row" id="search_result">
                        <div class="col-lg-12 table-responsive">
                            <table id="dataTable1" class="table table-striped table-bordered table-hover"
                                   style="margin-bottom: 20px; border-top: 3px solid #007bff">
                                <thead>
                                <tr style="background: #FFF; color: #337ab7; text-transform: uppercase; font-size: 12px">
                                    <th scope="col">Ticket ID</th>
                                    <th scope="col" style="width: 170px !important;">Update Date</th>
                                    <th scope="col">compliance No</th>
                                    <th scope="col">Compliance Level</th>
                                    <th scope="col">Category</th>
                                    <th scope="col">Sub Category</th>
                                    <th scope="col">Instruction type</th>
                                    <th scope="col">Applicable for</th>
                                    <th scope="col">Start Date</th>
                                    <th scope="col">Frequency</th>
                                    <th scope="col">Due Date</th>
                                    <th scope="col">Due Month</th>
                                    <th scope="col">Next Due Date</th>
                                    <th scope="col" style="font-size: 11px">Payment Penalty Implication Risk</th>
                                    <th scope="col">Compliance Owner</th>
                                    <th scope="col">Compliance Group</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                foreach ($data as $info) {
                                ?>
                                <tr>
                                    <td>
                                        <a href="{{ url('compliance-entry-view/' . $info->id) }}" target="_blank">
                                        <?php echo $info['ticket_id']?>
                                        </a>
                                    </td>
                                    <td><?php echo !empty($info->updated_at) ? date('d-M-Y h:i A', strtotime($info->updated_at)) : ''?></td>
                                    <td><?= $info['compliance_point_no']?></td>
                                    <td><?= $info['compliance_level']?></td>
                                    <td><?= $info['compliance_category']?></td>
                                    <td><?= $info['compliance_sub_category']?></td>
                                    <td><?= $info['instruction_type']?></td>
                                    <td><?= $info['compliance_applicable_for']?></td>
                                    <td><?= $info['start_date']?></td>
                                    <td><?= $info['frequency']?></td>
                                    <td><?= $info['due_date']?></td>
                                    <td><?= $info['due_month']?></td>
                                    <td><?= $info['next_due_date']?></td>
                                    <td><?= $info['payment_penalty_implication_risk']?></td>
                                    <td><?= $info['compliance_owner']?></td>
                                    <td><?= ($info['compliance_group_id'])??''?></td>
                                    <td><?= $info['status']?></td>
                                    <td>
                                        <a href="{{ url('compliance-entry-view/' . $info->id) }}"
                                           class="btn btn-success btn-sm text-white mt-2">View</a>
                                    </td>

                                </tr>
                                <?php } ?>
                                </tbody>
                            </table>

                            {{-- Pagination --}}
                            <div class="d-flex justify-content-center">
                                <span style="float: left; margin-top: .5%"><b>Showing {{$data->perPage()}} records out of {{$data->total()}} total</b>&nbsp;&nbsp;&nbsp;</span>
{{--                                {{$data->links('pagination::bootstrap-4')}}--}}
                                {{ $data->withQueryString()->links('pagination::bootstrap-4') }}


                            </div>

                        </div>



                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        #dataTable1_length {
            margin-bottom: 20px !important;
        }

        .dataTables1_paginate {
            margin-bottom: 50px;
        }

    </style>
@endsection



@section('script')
    <script>
        // $(document).ready(function () {
        //     $.fn.dataTable.moment('DD-MM-YYYYThh:mm:ss');
        //     $('#dataTable1').dataTable({
        //         responsive: true,
        //         "order": [[1, 'desc']]
        //     });
        // });

        {{--$('#search').keyup(function (e) {--}}
        {{--    let Url = "{{ route('compliance-entry') }}";--}}
        {{--    let searchValue = $('#search').val();--}}
        {{--    let element = $(document).find('#search_result');--}}
        {{--    if ($('#search').val().replace(/^\s+|\s+$/g, "").length != 0) {--}}
        {{--        $.ajax({--}}
        {{--            method: 'GET',--}}
        {{--            url: Url + '?search=' + searchValue,--}}
        {{--            dataType: 'html',--}}
        {{--            success: function (response) {--}}
        {{--                element.html(response);--}}
        {{--            },--}}
        {{--            error: function (res) {--}}
        {{--                console.log(res)--}}
        {{--            }--}}
        {{--        });--}}
        {{--    }--}}
        {{--});--}}
    </script>

@endsection