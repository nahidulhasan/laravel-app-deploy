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
            <div class="card active">
                <div class="card-header">{{ __('Periodic tickets lIST') }}</div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12 table-responsive">
                            <table id="" class="table table-striped table-bordered table-hover"
                                   style="margin-bottom: 20px; border-top: 3px solid #007bff">
                                <thead>
                                <tr style="background: #FFF; color: #337ab7">
                                    <th scope="col">ID</th>
                                    <th scope="col">Ticket Id</th>
                                    <th scope="col">Compliance Entry Id</th>
                                    <th scope="col">Title</th>
                                    <th scope="col">Type</th>
                                    <th scope="col">Message</th>
                                    <th scope="col">Create Date</th>
                                    <th scope="col">Update Date</th>
                                    <th scope="col">Action</th>
                                </tr>
                                </thead>
                                <tbody>

                                <?php
                                foreach ($data as $info) {
                                ?>
                                <tr>
                                    <td> <?= $info['id']?> </td>
                                    <td><?= $info['ticket_id']?></td>
                                    <td><?= $info['compliance_entry_id']?></td>
                                    <td><?= $info['title']?></td>
                                    <td><?= $info['type']?></td>
                                    <td><?= $info['message']?></td>
                                    <td><?php echo !empty($info->created_at) ? date('d-M-y h:i A', strtotime($info->created_at)) : '' ?></td>
                                    <td><?php echo !empty($info->updated_at) ? date('d-M-y h:i A', strtotime($info->updated_at)) : ''?></td>
                                    <td style="padding: 5px">

                                        <a href="{{ url('activity-logs-view/' . $info->id) }}"
                                           class="btn btn-success btn-sm">
                                            <svg class="nav-icon" style="width: 15px; height: 15px;">
                                                <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-low-vision"></use>
                                            </svg>
                                        </a>
                                    </td>

                                </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                <span style="float: left; margin-top: .5%"><b>Showing {{$data->perPage()}} records out of {{$data->total()}} total</b>&nbsp;&nbsp;&nbsp;</span>
                                {{$data->links('pagination::bootstrap-4')}}
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection



@section('script')


@endsection