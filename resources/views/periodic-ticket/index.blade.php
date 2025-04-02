@extends('layouts.app')

@section('content')
    <div class="container-fluid header mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
                <li class="breadcrumb-item">
                    <a href="{{route('dashboard')}}">Home</a>
                </li>
                <li class="breadcrumb-item active"><span>Periodic Tickets</span></li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card active">
                <div class="card-header">{{ __('Periodic tickets lIST') }} &nbsp;&nbsp;&nbsp;
                    <div class="card-tools">
                        <div class="row">
                            <div class="col-md-4">
                                <a href="{{route('periodic-tickets')}}" class="btn btn-primary">Reload</a>
                            </div>
                            <div class="col-md-8">
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
                                <tr style="background: #FFF; color: #337ab7">
                                    <th scope="col">ID</th>
                                    <th scope="col">Compliance Entry ID</th>
                                    <th scope="col">Periodic Ticket ID</th>
                                    <th scope="col">Compliance Owner</th>
                                    <th scope="col">Compliance Group</th>

                                    <th scope="col">Due Date</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Create Date</th>
                                    <th scope="col">Update Date</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach ($data as $info)

                                <tr>
                                    <td> {!! $info['id'] !!} </td>
                                    <td>
                                        <a href="{{ url('compliance-entry-view/' . $info['compliance_entry_id']) }}">
                                        {!! $info['compliance_entry_id']!!}
                                        </a>
                                    </td>
                                    <td>{!! $info['periodic_ticket_id']!!}</td>
                                    <td>{!! $info->complianceEntry['compliance_owner']!!}</td>
                                    <td>{!! $info->complianceEntry['compliance_group_id']!!}</td>
                                    <td>{!! $info['due_date']!!}</td>
                                    <td>{!! $info['status']!!}</td>
                                    <td><?php echo !empty($info['created_at']) ? date('d-M-y h:i A', strtotime($info->created_at)) : '' ?></td>
                                    <td><?php echo !empty($info['updated_at']) ? date('d-M-y h:i A', strtotime($info->updated_at)) : ''?></td>

                                </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                <span style="float: left; margin-top: .5%"><b>Showing {{$data->perPage()}} records out of {{$data->total()}} total</b>&nbsp;&nbsp;&nbsp;</span>
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
        {{--$('#search').keyup(function (e) {--}}
        {{--    let Url = "{{ route('periodic-tickets') }}";--}}
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
