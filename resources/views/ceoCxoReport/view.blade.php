@extends('layouts.app')

@section('content')
    <div class="container-fluid header mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
                <li class="breadcrumb-item">
                    <a href="{{route('dashboard')}}">Home</a>
                </li>
                <li class="breadcrumb-item active"><a href="{{route('ceo-cxo-report.index')}}">Ceo Cxo Report</a></li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card active">
                <div class="card-header">
                    <a href="{{route('ceo-cxo-report.index')}}" class="btn btn-sm btn-xs btn-warning">Back</a>&nbsp;
                </div>

                <div class="card-body">
                    <div class="row" id="search_result">
                        <div class="col-lg-12 table-responsive">
                            <pre> <b>Email Type :</b> {{$data['report_type']}}</pre>
                            <pre> <b>Email Receiver :</b> {{$data['receiver']}}</pre>
                            <pre> <b>Email Subject :</b> {{$data['subject']}}</pre>
                            <pre> <b>Email Body :</b> @php echo json_decode($data['email_body']) @endphp</pre>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
