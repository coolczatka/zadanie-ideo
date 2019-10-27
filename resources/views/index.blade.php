@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @foreach($trees as $tree)
            <div class="col-md-4">
                <div class="card mt-4">
                    <a href="{{route('detail',$tree->id)}}">
                    <div class="card-header">{{$tree->user->name}}</div>

                    <div class="card-body">
                        {{$tree->name}}
                    </div>
                    </a>
                </div>
            </div>
                @endforeach
        </div>
    </div>
@endsection
