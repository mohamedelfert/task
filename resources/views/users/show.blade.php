@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <div class="row" style="margin-top: 1rem;">
                            <div class="col-lg-12 margin-tb">
                                <div class="pull-left">
                                    <h2>{{ $title }}</h2>
                                </div>
                                <div class="pull-right mb-2">
                                    <a class="btn btn-success" href="{{ route('users.index') }}"> Return Back</a>
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered text-center">
                            <tr>
                                <th>Username</th>
                                <th>Phone Number</th>
                                <th>Status</th>
                            </tr>
                            <tr>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->phone }}</td>
                                <td>
                                    @if($user->verified)
                                        <span class="text-success">Verified</span>
                                    @else
                                        <span class="text-success">Unverified</span>
                                    @endif
                                </td>
                            </tr>
                        </table>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
