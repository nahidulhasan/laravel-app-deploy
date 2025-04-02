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
        <div class="col-md-12"  style=" background-color: #ffffff; margin-left: 20px !important; ">
        <div class="col-md-10" style="padding-left: 100px;; padding-top: 10px; padding-bottom: 40px;">
            <p style="text-align: center"> &nbsp;&nbsp;&nbsp; DynamicVariable for CXO Notification = <b> @php echo '{{monthYear}}   {{year}}   {{tableContent}}' @endphp </b></p><br>
            <div class="card active">
                <div class="card-header">@if(isset($data) && !empty($data->id)) Edit @else Add @endif Configuration</div>

                <div class="card-body">
                    @if($errors->any())
                        <h4>{{$errors->first()}}</h4>
                    @endif

                    @if(!empty($data->id))
                        <form method="POST" action="{{ route('cxo-notification-configs.update',$data->id) }}">
                            @method('PUT')
                            @else
                                <form method="POST" action="{{ route('cxo-notification-configs.store') }}">
                                    @endif
                                    {{ csrf_field() }}
                                    <div class="form-group mt-2">
                                        <label class="control-label" for="name">Name</label>
                                        <input type="text" name="name"  maxlength="255" id="name"
                                               class="form-control" autocomplete="off" placeholder="Enter name"
                                               value="{{ old('name', $data->name) }}">
                                        <div v-if="errors.username">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>

                                    <div class="form-group required mt-2">
                                        <label class="control-label" for="type">Type</label>
                                        <select class="form-select" name="type" required="required" id="type">
                                            <option value="">Select Type</option>
                                            <option value="Monthly"
                                                    @if ('Monthly' == old('type', $data->type)) selected="selected"
                                                    @endif>Monthly
                                            </option>
                                            <option value="Weekly"
                                                    @if ('Weekly' == old('type', $data->type)) selected="selected"
                                                    @endif>weekly
                                            </option>

                                            <option value="Daily"
                                                    @if ('Daily' == old('type', $data->type)) selected="selected"
                                                    @endif>Daily
                                            </option>
                                        </select>
                                        <div v-if="errors.type">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>

                                    <div class="form-group mt-2 @if($data->type !='weekly') d-none @endif " id="weekday">
                                        <label class="control-label" for="type">Select week day </label>
                                        <select class="form-select" name="week_day" required="required" id="weekdays">
                                            <option value="">Select Day</option>
                                            @foreach($days as $key=>$value)
                                            <option value="{{$key}}"
                                                    @if ($key == old('type', $data->day)) selected="selected"
                                                    @endif>{{$value}}
                                            </option>
                                                @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group mt-2">
                                        <label class="control-label" for="Day">Day</label>
                                        <input type="text" name="day" required maxlength="20" id="DayField"
                                               class="form-control"  placeholder="Enter Day"  value="{{ old('day', $data->day) }}">
                                        <div v-if="errors.day_count">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>

                                    <div class="form-group required mt-2">
                                        <label class="control-label" for="email_body">Email Subject  <span style="font-size: 11px;"><span></label>
                                        <textarea class="form-control" name="email_subject">{{old('email_subject', $data->email_subject)}}</textarea>
                                        <div v-if="errors.email_subject">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>

                                    <div class="form-group required mt-2">
                                        <label class="control-label" for="email_body">Email body  <span style="font-size: 11px;">( Fixed content )<span></label>
                                        <textarea id="emailBody" class="form-control" name="email_body" rows="8" cols="10">{{old('email_body', $data->email_body)}}</textarea>
                                        <div v-if="errors.email_body">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>

                                    <div class="form-group required mt-2">
                                        <label class="control-label" for="dynamic_content">Dynamic body  <span style="font-size: 11px;">( logical content )<span></label>
                                        <textarea id="" class="form-control" name="dynamic_content" rows="5">{{old('dynamic_content', $data->dynamic_content)}}</textarea>
                                        <div v-if="errors.dynamic_content">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>

                                    <div class="form-group required mt-2">
                                        <label class="control-label" for="status">Status</label>
                                        <select class="form-select" name="status" required="required" id="Status">
                                            <option value="Active"
                                                    @if ('Active' == old('type', $data->status)) selected="selected"
                                                    @endif>Active
                                            </option>
                                            <option value="Inactive"
                                                    @if ('Inactive' == old('type', $data->status)) selected="selected"
                                                    @endif>Inactive
                                            </option>
                                        </select>
                                        <div v-if="errors.username">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>

                                    <div class="form-group required mt-2">
                                        <label class="control-label" for="status">Email Receiver (To)</label>
                                        <select name="email_receiver[]" id="options" class="form-control select">
                                            @foreach($column_list as $option)
                                                <option value="{{ $option }}"
                                                >{{ $option }}</option>
                                            @endforeach
                                        </select>
                                        <div v-if="errors.username">
                                            <span class="text-danger"></span>
                                        </div>
                                    </div>

{{--                                    <div class="form-group required mt-2">--}}
{{--                                        <label class="control-label" for="status">Email Receiver (CC)</label>--}}
{{--                                        <input type="text"--}}
{{--                                               name="email_receiver_cc"--}}
{{--                                               class="form-control"--}}
{{--                                               placeholder="Enter Receiver Email"--}}
{{--                                               value="{{old('email_receiver_cc', $data->email_receiver_cc)}}"--}}
{{--                                        >--}}
{{--                                        <div v-if="errors.email_receiver">--}}
{{--                                            <span class="text-danger"></span>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
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
    </div>

@endsection
@section('script')
    <script src="https://cdn.ckeditor.com/4.19.0/standard/ckeditor.js"></script>
    <script src="{{asset('/assets/ck/ckeditor.js')}}"></script>
    <script src="{{asset('/select2/select2.min.js')}}"></script>
    <script src="{{asset('/select2/bootstrap.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('select').each(function () {
                $(this).select2({
                    theme: 'bootstrap4',
                    width: 'style',
                    placeholder: $(this).attr('placeholder'),
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
            let selectedValuesCC = <?php echo str_replace(' ','',json_encode($data->email_receiver_cc)) ?>;
            let selectedValuesTest = <?php echo str_replace(' ','',json_encode($data->email_receiver)) ?>;
            $('#options').val(selectedValuesTest).trigger('change');
            $('#email_receiver_cc').val(selectedValuesCC).trigger('change');
        });

        $('#type').on('change', function() {
            if(this.value =='Weekly') {
                $('#weekday').removeClass('d-none');
            }else{
                $('#weekday').addClass('d-none');
            }
        });

        $('#weekdays').on('change', function() {
          $('#DayField').val(this.value);
        });
        CKEDITOR.replace('emailBody', {
            height: 400,
            baseFloatZIndex: 10005,
            removeButtons: 'PasteFromWord'
        });
    </script>
@endsection
