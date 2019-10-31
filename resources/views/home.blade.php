@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card mt-4">
                    <div class="h3 content-center">Add tree</div>
                    <form action="{{route('add_tree')}}" method="post">
                        @csrf
                        <input class="form-control" placeholder="Tree name" type="text" name="name"/>
                        <button class="btn-primary" style="width: 100%;height: 2em;" type="submit">Add</button>
                    </form>
                </div>
            </div>
            @if(count($trees ?? [])>0)
            @foreach($trees ?? [] as $tree)
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
            @endif
        </div>
    </div>
@endsection

