@extends('layouts.app')

@section('content')

    <div class="container-fluid header mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb my-0 ms-2">
                <li class="breadcrumb-item">
                    <a href="{{route('dashboard')}}">Home</a>
                </li>
                <li class="breadcrumb-item active"><span>User Role</span></li>
            </ol>
        </nav>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card active">
                <div class="card-header">{{ __('User Role List') }} &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="card-tools">
                        <div class="row">
                            <div class="col-md-4">
                                <a class="btn btn-sm btn-primary text-right" href="{{route('user-role-create')}}"> Create</a>
                                <a class="btn btn-sm m-2 btn-warning text-right" href="{{route('user-role-sync')}}"> Sync</a><br>
                            </div>
                            <div class="col-md-8">
                                <div style="float: right">
                                    <form action="" method="GET">
                                        <div class="input-group input-group-sm" style="width: 250px;">
                                            <input name="search" value="{{ request('search') }}"  type="text" class="form-control float-right" placeholder="Search">
                                            <div class="input-group-append">
                                                <button style="color: white; margin-left: 5px" type="submit" class="btn btn-info">
                                                    Submit
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12  table-responsive">

                            <table id="" class="table table-striped table-bordered"
                                   style="margin-bottom: 20px; border-top: 3px solid #007bff">
                                <thead>
                                <tr style="background: #FFF; color: #337ab7">
                                    <th scope="col">ID</th>
                                    <th scope="col">Owner</th>
                                    <th scope="col">Role</th>
                                    <th scope="col">Line Manager</th>
                                    <th scope="col">EMT</th>
                                    <th scope="col">CXO</th>
                                    <th scope="col">CEO</th>
                                    <th scope="col" style="min-width: 165px">Created</th>
                                    <th style="min-width: 300px !important;">Actions</th>
                                </tr>
                                </thead>
                                <tbody>


                                @foreach ($data as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->compliance_owner }}</td>
                                        <td>{{ $user->role }}</td>
                                        <td>{{ $user->line_manager }}</td>
                                        <td>{{ $user->emt }}</td>
                                        <td>{{ $user->cxo }}</td>
                                        <td>{{ $user->ceo }}</td>
                                        <td>{{ $user->created_at }}</td>
                                        <td class="col" >
                                            <div class="btn-group">
                                            <a href="{{route('user-role-edit',$user->id)}}"
                                               class="btn btn-sm btn-primary" style="margin-right: 4px !important;">Edit</a>
                                            <form method="POST" action="{{route('user-role-delete',$user->id)}}">
                                                @csrf
                                                @method('DELETE')
                                                <input type="submit" class="btn btn-sm btn-danger delete-user"
                                                       value="Delete">
                                            </form>
                                            </div>
                                        </td>
                                    </tr>

                                @endforeach
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
    <script>
        // $(document).ready(function () {
        //     $('.delete-user').click(function (e) {
        //         e.preventDefault()
        //         if (confirm('Are you sure?')) {
        //             $(e.target).closest('form').submit()
        //         }
        //     });
        // });


    </script>
@endsection