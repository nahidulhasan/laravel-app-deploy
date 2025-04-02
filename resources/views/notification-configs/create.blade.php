@extends('layouts.app')

@section('content')

    <link href="{{asset('/select2/select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('/select2/select2-bootstrap4.css')}}" rel="stylesheet">

    <div class="container-fluid header mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
                <li class="breadcrumb-item">
                    <a href="{{route('dashboard')}}">Home</a>
                </li>
                <li class="breadcrumb-item active"><span>Setting</span></li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center">
        <div class="row">
            <div class="col-md-2">
            <a href="{{route('notification-configs.index')}}"
               class="btn btn-info btn-sm mb-3" style="color: #FFF">
                <i class="fa fa-arrow-left" aria-hidden="true"></i> &nbsp;
                Back
            </a>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card active">
                <div class="card-header">@if(isset($data) && !empty($data->id)) Edit @else Add @endif Setting</div>

                <div class="card-body">
                    @if($errors->any())
                        <h4>{{$errors->first()}}</h4>
                    @endif

                    @if(!empty($data->id))
                        <form method="POST" action="{{ route('notification-configs.update',$data->id) }}">
                            @method('PUT')
                            @else
                                <form method="POST" action="{{ route('notification-configs.store') }}">
                                    @endif

                                    {{ csrf_field() }}

                                    <div class="form-group mt-2">
                                        <label class="control-label" for="Day">Day</label>
                                        <input type="text" name="day_count" required maxlength="30" id="Day"
                                               class="form-control" autocomplete="off" placeholder="Enter Day"
                                               value="{{ old('day_count', $data->day_count) }}">
                                        <div v-if="errors.day_count">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>

                                    <div class="form-group required mt-2">
                                        <label class="control-label" for="type">Type</label>
                                        <select class="form-select" name="type" required="required" id="type">
                                            <option value="Before"
                                                    @if ('Before' == old('type', $data->type)) selected="selected"
                                                    @endif>Before
                                            </option>
                                            <option value="After"
                                                    @if ('After' == old('type', $data->type)) selected="selected"
                                                    @endif>After
                                            </option>
                                        </select>
                                        <div v-if="errors.type">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>

                                    <div class="form-group required mt-2">
                                        <label class="control-label" for="template_id">Template <span style="font-size: 8px;">( Subject => Workflow )<span></label>
                                        <select class="form-select" name="template_id" required="required"
                                                id="template_id">
                                            @foreach(@$template['data'] as $key => $info )
                                                @if($info['status']=='Active')
                                                <option value="{{$info['id']}}" @if ($info['id'] == old('type', $data->template_id)) selected="selected"
                                                        @endif>{{$info['subject']}}
                                                    => {{$info['workflow_id']['name']}} </option>
                                                @endif
                                            @endforeach
                                        </select>
                                        <div v-if="errors.template_id">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>

                                    <div class="form-group required mt-2">
                                        <label class="control-label" for="status">Status</label>
                                        <select class="form-select" name="status" required="required" id="Status">
                                            <option value="Active"
                                                    @if ('Active' == old('status', $data->status)) selected="selected"
                                                    @endif>Active
                                            </option>
                                            <option value="Inactive"
                                                    @if ('Inactive' == old('status', $data->status)) selected="selected"
                                                    @endif>Inactive
                                            </option>
                                        </select>
                                        <div v-if="errors.status">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>

                                    <div class="form-group required mt-2">
                                        <label class="control-label" for="status">Email Receiver (To)</label>
                                        <select  name="email_receiver[]" id="email_receiver_to" multiple
                                                class="form-control select" >
                                            @foreach($column_list as $option)
                                                <option value="{{ $option }}"
                                                        >{{ $option }}</option>
                                            @endforeach
                                        </select>
                                        <div v-if="errors.email_receiver">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>

                                    <div class="form-group required mt-2">
                                        <label class="control-label" for="status">Email Receiver (CC)</label>
                                        <select name="email_receiver_cc[]" id="email_receiver_cc"  class="form-control select">

                                            @foreach($column_list as $option)
                                                <option value="{{ $option }}"
                                                >{{ $option }}</option>
                                            @endforeach
                                        </select>
                                        <div v-if="errors.email_receiver_cc">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>

                                    <div class="form-group row mt-3">
                                        <div class="col-sm-10">
                                            <button type="submit" class="btn btn-primary">Save</button>
                                        </div>
                                    </div>
                                </form>
                </div>
            </div>
        </div>

    </div>

@endsection
@section('script')
    <script src="{{asset('/select2/select2.min.js')}}"></script>
    <script src="{{asset('/select2/bootstrap.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('select').each(function () {
                $(this).select2({
                    theme: 'bootstrap4',
                    width: 'style',
                    placeholder: "Select Email",
                    allowClear: Boolean($(this).data('allow-clear')),
                });
            });
            $('#email_receiver_cc').each(function () {
                $(this).select2({
                    theme: 'bootstrap4',
                    width: 'style',
                    placeholder: "Select Email",
                    allowClear: Boolean($(this).data('allow-clear')),
                    multiple:true
                });
            });
            let selectedValuesTo = <?php echo str_replace(' ','',json_encode($data->email_receiver)) ?>;
            let selectedValuesCC = <?php echo str_replace(' ','',json_encode($data->email_receiver_cc)) ?>;
            $('#email_receiver_to').val(selectedValuesTo).trigger('change');
            $('#email_receiver_cc').val(selectedValuesCC).trigger('change');
        });
    </script>
@endsection
