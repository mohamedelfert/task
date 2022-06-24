@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                @foreach ($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
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

                        <form class="form-horizontal" method="post" action="{{ route('users.store') }}">
                            @csrf
                            <div class="col-lg-12 mb-3">
                                <label class="control-label" for="username">Username</label>
                                <input class="form-control" id="username" type="text" name="username" placeholder="Username">
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label class="control-label" for="phone">Phone Number</label>
                                <input class="form-control" id="phone" type="text" name="phone" placeholder="Phone Number">
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label class="control-label" for="password">Password</label>
                                <input class="form-control" id="password" type="password" name="password" placeholder="Password">
                            </div>
                            <div class="col-lg-12 mb-3">
                                <label class="control-label" for="password_confirmation">Password Confirmation</label>
                                <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" placeholder="Password Confirmation">
                            </div>
                            <input class="form-control btn btn-block btn-success" type="submit" name="submit" value="Add User">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
