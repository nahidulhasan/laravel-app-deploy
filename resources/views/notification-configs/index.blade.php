@extends('layouts.app')

@section('content')
    <link href="{{asset('css/jquery.dataTables.css')}}" rel="stylesheet">
    <div class="container-fluid header mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
                <li class="breadcrumb-item">
                    <a href="{{route('dashboard')}}">Home</a>
                </li>
                <li class="breadcrumb-item active"><span> Notification config</span></li>
            </ol>
            <br>
            <br>
            <p style="text-align: right"> &nbsp;&nbsp;&nbsp; DynamicVariable for CXO Notification =  @php echo '{{monthYear}}   {{year}}   {{tableContent}}' @endphp</p>
        </nav>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card active">
                <div class="card-header">
                    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active"
                                    id="home-tab"
                                    data-coreui-toggle="tab"
                                    data-coreui-target="#reminder"
                                    type="button"
                                    role="tab"
                                    aria-controls="reminder"
                                    aria-selected="true">
                                Reminder Notification
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                    id="profile-tab"
                                    data-coreui-toggle="tab"
                                    data-coreui-target="#CXO"
                                    type="button"
                                    role="tab"
                                    aria-controls="CXO"
                                    aria-selected="false"
                                    tabindex="-1">
                                CXO Notification
                            </button>
                        </li>

                    </ul>
                </div>

                <div class="card-body">
                    @if(Session::has('message'))
                        <div class="alert alert-success" role="alert" aria-live="assertive" aria-atomic="true">
                            <div class="d-flex">
                                <div class="toast-body">
                                    {{ Session::get('message') }}
                                </div>
                                <button type="button" class="btn-close me-2 m-auto" data-coreui-dismiss="toast" aria-label="Close"></button>
                            </div>
                        </div>
                    @endif
                    <div class="row">
{{--                        ================Tab ==========================--}}
                        <div class="tab-content rounded-bottom">
                            <div class="tab-pane p-3 active preview" role="tabpanel" id="preview-1015">
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade active show" id="reminder" role="tabpanel" aria-labelledby="reminder-tab">
                                        <div class="col-lg-12 table-responsive">
                                            <a href="{{route('notification-configs.create')}}"
                                               class="btn btn-success btn-sm mt-1 mb-2" style="color: #FFF">
                                                <svg class="nav-icon" style="width: 15px; height:15px;">
                                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-plus"></use>
                                                </svg>
                                                Add new
                                            </a>
                                            <table id="dataTable12" class="table dataTable table-striped table-bordered table-hover"
                                                   style="margin-bottom: 20px; border-top: 3px solid #007bff">
                                                <thead>
                                                <tr style="background: #FFF; color: #337ab7; text-transform: uppercase; font-size: 12px">
                                                    <th>SL</th>
                                                    <th scope="col" style="width: 170px !important;">Update Date</th>
                                                    <th scope="col">Day</th>
                                                    <th scope="col">Type</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Template</th>
                                                    <th scope="col">Email Receiver</th>
                                                    <th scope="col">CC Email</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($data as $info) {
                                                ?>
                                                <tr>
                                                    <td>{{$info->id}}</td>
                                                    <td><?php echo !empty($info->updated_at) ? date('d-M-Y h:i A', strtotime($info->updated_at)) : ''?></td>
                                                    <td><?= $info['day_count']?></td>
                                                    <td><?= $info['type']?></td>
                                                    <td><?= $info['status']?></td>
                                                    <td><?= $info['template_id']?></td>
                                                    <td><?= $info['email_receiver'] ?></td>
                                                    <td><?= $info['email_receiver_cc'] ?></td>
                                                    <td>
                                                        <a href="{{ route('notification-configs.show',$info->id) }}"
                                                           class="btn btn-success btn-sm text-white mt-2"><i
                                                                    class="fa fa-pencil"></i></a> &nbsp;
                                                        <form action="{{ route('notification-configs.destroy',$info->id) }}" method="POST"
                                                              style="float: left">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" onclick="return confirm('Are you sure?')"
                                                                    class="btn btn-danger btn-sm text-white mt-2"><i class="fa fa-trash"></i></button>&nbsp;&nbsp;
                                                        </form>
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
                                    <div class="tab-pane fade" id="CXO" role="tabpanel" aria-labelledby="CXO-tab">
                                        <div class="col-lg-12 table-responsive">
                                            <a href="{{route('cxo-notification-configs.create')}}"
                                               class="btn btn-success btn-sm mt-1 mb-2" style="color: #FFF">
                                                <svg class="nav-icon" style="width: 15px; height:15px;">
                                                    <use xlink:href="vendors/@coreui/icons/svg/free.svg#cil-plus"></use>
                                                </svg>
                                                Add new
                                            </a>


                                            <table id="dataTable12" class="table dataTable table-striped table-bordered table-hover"
                                                   style="margin-bottom: 20px; border-top: 3px solid #007bff">
                                                <thead>
                                                <tr style="background: #FFF; color: #337ab7; text-transform: uppercase; font-size: 12px">
                                                    <th>SL</th>
                                                    <th scope="col" style="width: 170px !important;">Update Date</th>
                                                    <th scope="col">Config Name</th>
                                                    <th scope="col">Fixed content</th>
                                                    <th scope="col">logical content</th>
                                                    <th scope="col">Type</th>
                                                    <th scope="col">Schedule Time</th>
                                                    <th scope="col">Email Receiver</th>
                                                    <th scope="col">CC Email</th>
                                                    <th scope="col">Action</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach ($cxo_notification as $info) {
                                                ?>
                                                <tr>
                                                    <td>{{$info->id}}</td>
                                                    <td><?php echo !empty($info->updated_at) ? date('d-M-Y h:i A', strtotime($info->updated_at)) : ''?></td>
                                                    <td><?= $info['name']?></td>
                                                    <td><?= $info['email_body']?></td>
                                                    <td><?= $info['dynamic_content']?></td>
                                                    <td><?= $info['type']?></td>
                                                    <td>Time</td>
                                                    <td><?= $info['email_receiver'] ?></td>
                                                    <td><?= $info['email_receiver_cc'] ?></td>
                                                    <td>
                                                        <a href="{{ route('cxo-notification-configs.show',$info->id) }}"
                                                           class="btn btn-success btn-sm text-white mt-2"><i
                                                                    class="fa fa-pencil"></i></a> &nbsp;
                                                        <form action="{{ route('cxo-notification-configs.destroy',$info->id) }}" method="POST"
                                                              style="float: left">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" onclick="return confirm('Are you sure?')"
                                                                    class="btn btn-danger btn-sm text-white mt-2"><i class="fa fa-trash"></i></button>&nbsp;&nbsp;
                                                        </form>
                                                    </td>

                                                </tr>
                                                <?php } ?>
                                                </tbody>
                                            </table>
                                            <div class="d-flex justify-content-center">
                                                <span style="float: left; margin-top: .5%"><b>Showing {{$cxo_notification->perPage()}} records out of {{$cxo_notification->total()}} total</b>&nbsp;&nbsp;&nbsp;</span>
                                                {{$cxo_notification->links('pagination::bootstrap-4')}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <style>
        .nav-item > .active{
            color: green !important;
        }

        .card-header {
            padding: 0px !important;
            margin-bottom: 0px !important;
            color: var(--cui-card-cap-color);
            background: none !important;
            border-bottom: none !important;
        }
        .nav-tabs > li > button{
            min-height: 60px !important;
        }
</style>
@endsection



@section('script')

@endsection