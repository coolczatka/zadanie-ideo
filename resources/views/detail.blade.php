@extends('layouts.app')

@section('content')
    <script>
        function init() {
            x = fetch('/');

        }
        function add_plus_listeners() {
            let arrows = document.getElementsByClassName('plus');
            Array.from(arrows).forEach(function (arrow) {
                arrow.addEventListener('click',expand);
            });
        }
        function expand(e) {
            e.target.innerHTML = 'v';
            e.target.removeEventListener('click',expand);
            e.target.addEventListener('click',reduce);
            let parent = e.target.parentNode;
            let x = document.createElement('div');
            x.className = "card-body pt-0 pb-0";
            
            let child = "";
            parent.appendChild(child);

        }
        function buildElement() {
            let result = document.createElement('div');

            
        }
        function reduce(e) {
            e.target.innerHTML = ">";
            e.target.removeEventListener('click',reduce);
            e.target.addEventListener('click',expand);
        }
    </script>
    <div class="container">
        @foreach($tree->nodes as $node)
        <div class="row mt-1">
            <div class="col-12">
                <div class="card node">
                    <div class="card-body pt-0 pb-0" id="{{$node->id}}">@if(count($node->get_direct_children())>0)<span class="plus">></span>@endif{{$node->id}} - {{$node->name}}
                        @if($is_owner) <button class="btn-success">Add</button> <button class="btn-danger">Del</button> <button class="btn-warning">Edit</button> @endif</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <script>add_plus_listeners()</script>
@endsection
