@extends('layouts.app')

@section('content')
    <div class="container">
        @guest<div class="row justify-content-center" style="text-align: center;"><div class="col-md-12"> <div class="h3">Log in to add your own tree</div> </div></div> @endguest
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
