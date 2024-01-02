@extends('layouts.app')

@section('content')  
<main class="app-content">
    <div class="card"> 
        <div class="card-body">
            <h1 class="h4 mb-3">Users List</h1>
            <form class="form-inline mb-3" method="GET">
                <div class="form-group">
                    <input type="text" class="form-control mr-3" id="filter" name="filter" placeholder="User name..." value="{{$filter}}">
                </div>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
            @if(session()->has('edit_user_error'))
                <p class="text-danger">
                    {{ session()->get('edit_user_error') }}
                </p>
            @endif
            @if(session()->has('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div>
            @endif
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>@sortablelink('first_name', 'Name')</th>
                            <th>@sortablelink('email', 'Email')</th>
                            <th>@sortablelink('email_verified', 'Verified')</th>
                            <th>Recordings</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($data) && $data->count())
                            @foreach($data as $key => $value)
                                <tr>
                                    <td>{{ $value->first_name }}</td>
                                    <td>{{ $value->email }}</td>
                                    <td>{{ $value->email_verified ? 'Verified' : 'Not Verified' }}</td>
                                    <td>{{ $value->recordings }}</td>
                                    <td class="text-center">
                                        <a href="{{ url('/user/' . $value->id . '/edit') }}" class=""><i class="fa fa-edit"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="10" class="text-center">There are no data.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="row align-items-center">
                <div class="col-sm-12 col-md-5">
                    <p class="m-0">Displaying {{$data->count()}} of {{ $data->total() }} User(s).</p>
                </div>
                <div class="col-sm-12 col-md-7">
                    {!! $data->links() !!}
                </div>
            </div>
        </div>
    </div>

    
</main>
@endsection