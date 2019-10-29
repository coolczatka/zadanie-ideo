@extends('layouts.app')

@section('content')
    <script>
        function getChildren(id,url) {
            fetch(url).then(resp=>resp.json()).then(resp =>{
                resp.children.forEach(obj =>{

                    let container;
                    if(id===0) {
                        container = document.getElementById('container');
                    }
                    else {
                        container = document.getElementById(id);
                    }
                    let element = document.createElement('div');
                    element.setAttribute('id',obj.id);
                    element.classList.add('node');
                    element.innerHTML='<div class="row mt-1">' +
                        '                <div class="col-12">' +
                        '                    <div class="card node_body">' +
                        '                        <div class="card-body pt-0 pb-0"><span  class="plus">></span>'+obj.id+' - '+obj.name +
                        '                            @if($is_owner)<div class="float-right"><button class="btn-success">Add child</button> <button class="btn-danger">Delete node</button> <button class="btn-warning">Edit node</button></div>@endif</div>' +
                        '                    </div>' +
                        '                </div>' +
                        '            </div>';
                    element.querySelector('.plus').addEventListener('click',expand);
                    element.querySelector('.btn-success').addEventListener('click',add_create_form);
                    element.querySelector('.btn-danger').addEventListener('click',add_delete_form);
                    container.appendChild(element);
                })
            });

        }

        function findClosestParentId(element) {
            while(!element.hasAttribute('id'))
                element =element.parentElement;
            return element.id;
        }

        function expand(e) {
            e.target.innerHTML = 'v';
            e.target.removeEventListener('click',expand);
            e.target.addEventListener('click',reduce);
            let id = findClosestParentId(e.target);
            getChildren(id,"{{route('get_nodes',['tree_id'=>last(explode('/',request()->url()))])}}"+"?id="+id);

        }
        function buildElement() {
            let result = document.createElement('div');
        }
        function reduce(e) {
            e.target.innerHTML = ">";
            e.target.removeEventListener('click',reduce);
            e.target.addEventListener('click',expand);
            let id = findClosestParentId(e.target);
            let parent = document.getElementById(id);
            parent.querySelectorAll('.node').forEach(el =>{
                if(el!==parent)
                    el.remove();
            });
        }

        function add_create_form(e){
            let id = findClosestParentId(e.target)
            let element = document.getElementById(id);
            let form = document.createElement('form');
            form.setAttribute('method','post');
            let temp = window.location.href.split('/');
            let tree_id = temp[temp.length-1];
            form.innerHTML = '<label for="name">Name</label><input type="text" id="name" name="name" />' +
                '<input type="hidden" name="parent_id" value="'+id+'"/> ' +
                '<input type="hidden" name="tree_id" value="'+tree_id+'"/>' +
                '<button type="submit">Add</button>';
            element.appendChild(form);
            form.addEventListener('submit',create);
        }

        function create(e){
            e.preventDefault();
            let form = e.target;
            let formData = new FormData();
            for(let i=0;i<form.length;i++){
                formData.append(form[i].name,form[i].value);
            }
            let url = "{{route('create_node')}}";
            fetch(url,{
                method:'post',
                body:formData
            }).then(resp => resp.json()).then(resp => {
                let parent = form.parentNode;
                form.remove();
                parent.querySelector('.plus').click();
                parent.querySelector('.plus').click();

            })
        }

        function del(e){
            e.preventDefault();
            let form = e.target;
            let formData = new FormData();
            for(let i=0;i<form.length;i++){
                formData.append(form[i].name,form[i].value);
            }
            let url = "{{route('delete_node')}}";
            fetch(url,{
                method:'post',
                body:formData
            }).then(resp => resp.json()).then(resp => {
                console.log(resp);
                let parent = form.parentNode.parentNode;
                if(parent.classList.contains('.nodes')){
                    document.querySelector('#nodes').innerHTML="";
                    getChildren(0,"{{route('get_nodes',['tree_id'=>last(explode('/',request()->url()))])}}"+"?id=0");
                }
                else {
                    form.remove();

                    parent.querySelector('.plus').click();
                    parent.querySelector('.plus').click();

                }
            })
        }

        function add_delete_form(e){
            let id = findClosestParentId(e.target)
            let element = document.getElementById(id);
            let form = document.createElement('form');
            form.setAttribute('method','post');
            let temp = window.location.href.split('/');
            let tree_id = temp[temp.length-1];
            form.innerHTML =
                '<div class="radio"><label for="option1"><input type="radio" value="1" id="option1" name="with" />Delete with children</label></div>' +
                '<div class="radio"><label for="option2"><input type="radio" value="0" id="option2" name="with" />' +
                'Add children to parent of removed node</label></div>' +
                '<input type="hidden" name="tree_id" value="'+tree_id+'"/>' +
                '<input type="hidden" name="id" value="'+id+'"/>' +
                '<button class="btn-danger" type="submit">Remove</button>';
            element.appendChild(form);
            form.addEventListener('submit',del);
        }

    </script>
    <div class="container" id="container">
        <div class="control row mt-1">
            <div class="col-md-3"><button>xd</button></div>
            <div class="col-md-3"></div>
            <div class="col-md-3"></div>
        </div>
        <div class="nodes" id="nodes">
        </div>
    </div>
    <script>
            getChildren(0,"{{route('get_nodes',['tree_id'=>last(explode('/',request()->url()))])}}"+"?id=0");
    </script>
@endsection
