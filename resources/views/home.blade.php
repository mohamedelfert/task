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
                                    <h2>{{ $title }} : ( <span class="text-primary"> {{ $users->total() }} </span> ) User</h2>
                                </div>
                                @if(auth()->user()->role === 'admin')
                                    <div class="pull-right mb-2">
                                        <a class="btn btn-success" href="{{ route('users.create') }}"> Create New User</a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <table class="table table-bordered text-center">
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Phone Number</th>
                                <th>Status</th>
                                <th width="280px">Action</th>
                            </tr>
                            @foreach ($users as $index => $user)
                                <tr>
                                    <td>{{ $index  + 1 }}</td>
                                    <td>{{ $user->username }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>{{ $user->verified }}</td>
                                    <td>
                                        @if(auth()->user()->role === 'admin' OR auth()->user()->id === $user->id)
                                            <a class="btn btn-info" href="{{ route('users.show',$user->id) }}">Show</a>
                                            <a class="btn btn-primary" href="{{ route('users.edit',$user->id) }}">Edit</a>
                                            @if(auth()->user()->role === 'admin')
                                                <a class="modal-effect btn btn-danger" data-effect="effect-scale"
                                                   data-toggle="modal" href="#delete{{ $user->id }}">Delete</a>
                                            @else
                                                <a class="btn btn-danger disabled">Delete</a>
                                            @endif
                                        @else
                                            <a class="btn btn-info disabled">Show</a>
                                            <a class="btn btn-primary disabled">Edit</a>
                                            <a class="btn btn-danger disabled">Delete</a>
                                        @endif
                                    </td>
                                </tr>

                                <!-- Delete -->
                                <div class="modal fade" id="delete{{ $user->id }}">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content modal-content-demo">
                                            <div class="modal-header">
                                                <h6 class="modal-title">Delete</h6>
                                                <button aria-label="Close" class="close" data-dismiss="modal"
                                                        type="button"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                            <form action="{{ route('users.destroy',$user->id) }}"
                                                  method="post">
                                                {{ method_field('delete') }}
                                                {{ csrf_field() }}
                                                <div class="modal-body">
                                                    <p>Are You Sure About The Deletion ?</p><br>
                                                    <input type="hidden" name="id" id="id" value="{{ $user->id }}">
                                                    <input class="form-control" name="username" id="username" type="text"
                                                           value="{{ $user->username }}" readonly>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">Cancel</button>
                                                    <button type="submit"
                                                            class="btn btn-danger">Confirm</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Delete -->

                            @endforeach
                        </table>

                        {{ $users->links("pagination::bootstrap-4") }}

{{--                        @if ($users->hasPages())--}}
{{--                            <nav>--}}
{{--                                <ul class="pagination">--}}
{{--                                    --}}{{-- Previous Page Link --}}
{{--                                    @if ($users->onFirstPage())--}}
{{--                                        <li class="page-item disabled" aria-disabled="true">--}}
{{--                                            <span class="page-link">@lang('pagination.previous')</span>--}}
{{--                                        </li>--}}
{{--                                    @else--}}
{{--                                        <li class="page-item">--}}
{{--                                            <a class="page-link" href="{{ $users->previousPageUrl() }}" rel="prev">@lang('pagination.previous')</a>--}}
{{--                                        </li>--}}
{{--                                    @endif--}}

{{--                                    --}}{{-- Next Page Link --}}
{{--                                    @if ($users->hasMorePages())--}}
{{--                                        <li class="page-item">--}}
{{--                                            <a class="page-link" href="{{ $users->nextPageUrl() }}" rel="next">@lang('pagination.next')</a>--}}
{{--                                        </li>--}}
{{--                                    @else--}}
{{--                                        <li class="page-item disabled" aria-disabled="true">--}}
{{--                                            <span class="page-link">@lang('pagination.next')</span>--}}
{{--                                        </li>--}}
{{--                                    @endif--}}
{{--                                </ul>--}}
{{--                            </nav>--}}
{{--                        @endif--}}

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
