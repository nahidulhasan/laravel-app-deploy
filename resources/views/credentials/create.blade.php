@extends('layouts.app')

@section('content')

    <div class="container-fluid header mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
                <li class="breadcrumb-item">
                    <a href="{{route('dashboard')}}">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{route('index')}}">Credential</a>
                </li>

                <li class="breadcrumb-item active">
                <span>
                  {{--  {{$header}}--}}
                </span>
                </li>
            </ol>
        </nav>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Create') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('create-token') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Client Name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            {{--<div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Slug') }}</label>

                                <div class="col-md-6">
                                    <input id="slug" type="slug" class="form-control @error('slug') is-invalid @enderror" name="slug" value="{{ old('slug') }}" required autocomplete="slug">

                                    @error('slug')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>--}}

                            <div class="row mb-3">
                                <label for="api_key" class="col-md-4 col-form-label text-md-end">{{ __('Client API Key') }}</label>

                                <div class="col-md-6">
                                    <input id="api_key" type="text"  class="form-control @error('api_key') is-invalid @enderror" name="api_key" required>

                                    @error('api_key')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Client Secret') }}</label>

                                <div class="col-md-6">
                                    <input id="secret" type="text" class="form-control @error('secret') is-invalid @enderror" name="secret" value="{{ old('secret') }}" readonly>

                                    @error('secret')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>


                            <div class="row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Submit') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
