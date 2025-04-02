@extends('layouts.app')


@section('content')
<div class="container-fluid header mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb my-0 ms-2">
            <li class="breadcrumb-item">
                <a href="{{route('dashboard')}}">Home</a>
            </li>
            <li class="breadcrumb-item">
                <a href="{{route('users')}}">Users</a>
            </li>
            
            <li class="breadcrumb-item active">
                <span>
                    {{$header}}
                </span>
            </li>
        </ol>
    </nav>
</div>
<div class="row justify-content-center">
    <div class="col-md-12">
        <div class="card active">
            <div class="card-header">{{ $header}}</div>
            <div class="card-body">
                @include('users.form')
            </div>
        </div>
    </div>
</div>
@endsection