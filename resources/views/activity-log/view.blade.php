@extends('layouts.app')

@section('content')
    <div class="container-fluid header mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
                <li class="breadcrumb-item">
                    <a href="{{route('dashboard')}}">Home</a>
                </li>
                <li class="breadcrumb-item active"><span>Activity Logs</span></li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <a href="{{route('activity-logs')}}" type="button" class="btn btn-default btn-outline-dark mb-4">Back To List</a>
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
                                    <td width="250">Ticket Id</td>
                                    <td>{!! $data->ticket_id!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Compliance Entry Id</td>
                                    <td>{!! $data->id!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Title</td>
                                    <td>{!! $data->title!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Type</td>
                                    <td>{!! $data->type!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Message</td>
                                    <td>{!! $data->message!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Create Date</td>
                                    <td>{!! $data->id!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Update Date</td>
                                    <td>{!! $data->id!!}</td>
                                </tr>
                                <tr>
                                    <td width="250">Created By</td>
                                    <td>{!! $data->created_by!!}</td>
                                </tr><tr>
                                    <td width="250">Payload</td>
                                    <td>
                                       {{$data->payload}}

                                    </td>
                                </tr>
                            </table>

                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
