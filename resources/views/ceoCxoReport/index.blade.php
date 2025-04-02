@extends('layouts.app')

@section('content')
    <div class="container-fluid header mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
                <li class="breadcrumb-item">
                    <a href="{{route('dashboard')}}">Home</a>
                </li>
                <li class="breadcrumb-item active"><span>Ceo Cxo Report</span></li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card active">
                <div class="card-header">{{ __('Ceo Cxo Report list') }}
                    <form action="" method="GET" class="pull-right">
                        <input type="text" name="search_field" value="{{old('search_field')}}" placeholder="Search..."  autocomplete="off">
                        <input type="hidden" value="1" name="page" placeholder="Search..." >
                        <button type="submit">Search</button>
                    </form>
                </div>

                <div class="card-body">
                    <div class="row" id="search_result">
                        <div class="col-lg-12 table-responsive">
                            <table id="dataTable1" class="table table-striped table-bordered table-hover"
                                   style="margin-bottom: 20px; border-top: 3px solid #007bff">
                                <thead>
                                <tr style="background: #FFF; color: #337ab7">
                                    <th scope="col">ID</th>
                                    <th scope="col">Report Type</th>
                                    <th scope="col">Subject</th>
                                    <th scope="col">Compliance Owner</th>
                                    <th scope="col">Send Time</th>
                                    <th scope="col">Receive Time</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach ($data as $info)

                                <tr>
                                    <td> {!! $info['id'] !!} </td>
                                    <td> {!! $info['report_type'] !!} </td>
                                    <td>
                                        {!! $info['subject']!!}
                                    </td>
                                    <td>{!! substr($info['receiver'], 0, 100);!!}</td>
                                    <td><?php echo !empty($info['created_at']) ? date('d-M-y h:i A', strtotime($info->created_at)) : '' ?></td>
                                    <td><?php echo !empty($info['updated_at']) ? date('d-M-y h:i A', strtotime($info->created_at)) : '' ?></td>
                                    <td>{!! $info['status']!!}</td>
                                    <td>
                                        <a href="{{ route('ceo-cxo-report.show', $info['id']) }}">
                                        <button href="" class="btn btn-xs btn-info">view</button>
                                        </a>
                                    </td>

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
