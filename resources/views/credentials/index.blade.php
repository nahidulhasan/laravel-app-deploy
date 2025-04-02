@extends('layouts.app')

@section('content')
    <div class="container-fluid header mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
                <li class="breadcrumb-item">
                    <a href="{{route('dashboard')}}">Home</a>
                </li>
                <li class="breadcrumb-item active"><span>Credential</span></li>
            </ol>
        </nav>
    </div>


    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Client Credential') }}</div>

                    <div class="card-body">

                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h5 class="card-title">Client Credential  List </h5>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex flex-wrap align-items-center justify-content-end gap-2 mb-3">
                                            <div>
                                                <a href="{{ route('create-token') }}" class="btn btn-primary"><i class="bx bx-plus me-1"></i> Add New</a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="">
                                            <div class="table-responsive">
                                                <table class="table project-list-table table-nowrap align-middle table-borderless">
                                                    <thead>
                                                    <tr>
                                                        <th scope="col">ID</th>
                                                        <th scope="col">Name</th>
                                                        <th scope="col">API-KEY</th>
                                                        <th scope="col">Secret</th>
                                                        <th scope="col">Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>

                                                    <?php
                                                    foreach ($data as $info) {
                                                        ?>
                                                    <tr>
                                                        <td> <?= $info['id']?> </td>
                                                        <td><?= $info['name']?></td>
                                                        <td><?= $info['api_key']?></td>
                                                        <td><?= $info['secret']?></td>
                                                        <td>
                                                            <a href="{{ route('edit', $info['id']) }}" class="btn btn-warning"><i class="bx bx-plus me-1"></i> Edit</a>
                                                        </td>
                                                    </tr>

                                                    <?php } ?>
                                                    </tbody>
                                                </table>
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
@endsection
