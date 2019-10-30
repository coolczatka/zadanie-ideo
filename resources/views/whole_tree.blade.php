@extends('layouts.app')

@section('content')
    <script>
        var sort_column = '';
        function init_f() {
            document.getElementById('add_mn_button').addEventListener('click',add_create_form);
            document.getElementById('sort_name_button').addEventListener('click',sort_name);
            document.getElementById('sort_id_button').addEventListener('click',sort_id);
        }
        function sort_name() {
            sort_column = 'name';
            document.querySelector('#nodes').innerHTML="";
            getChildren(0)
        }
        function sort_id() {
            sort_column = 'id';
            document.querySelector('#nodes').innerHTML="";
            getChildren(0)
        }
        function getChildren(id) {
            let url = "{{route('get_nodes',['tree_id'=>last(explode('/',request()->url()))])}}"+"?id="+id;
            if(sort_column !== '')
                url+=("&sort_column="+sort_column);
            fetch(url).then(resp=>resp.json()).then(resp =>{
                console.log(resp);
                resp.children.forEach(obj =>{
                    let container;
                    if(id===0)
                        container = document.getElementById('nodes');
                    else
                        container = document.getElementById(id);
                    let element = document.createElement('div');
                    element.setAttribute('id',obj.id);
                    element.classList.add('node');
                    element.innerHTML='<div class="row mt-1">' +
                        '                <div class="col-12">' +
                        '                    <div class="card node_body">' +
                        '                        <div class="card-body pt-0 pb-0"><span  class="plus">></span>'+obj.id+' - '+obj.name +
                        '                            @if($is_owner)<div class="float-right"><button class="btn-success nod">Add child</button> <button class="btn-danger">Delete node</button> <button class="btn-warning">Edit node</button></div>@endif</div>' +
                        '                    </div>' +
                        '                </div>' +
                        '            </div>';
                    element.querySelector('.plus').addEventListener('click',expand);
                    if(element.querySelector('.btn-success') !== null) {
                        element.querySelector('.btn-success').addEventListener('click', add_create_form);
                        element.querySelector('.btn-danger').addEventListener('click', add_delete_form);
                        element.querySelector('.btn-warning').addEventListener('click', add_edit_form);
                    }
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
            getChildren(id);

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
            let id;
            let element;
            if(e.target.classList.contains('nod')) {
                id = findClosestParentId(e.target);
                element = document.getElementById(id);
            }
            else {
                id = 0;
                element = document.getElementById('nodes');
            }
            let form = document.createElement('form');
            let old_form = document.getElementById('container').querySelector('form');
            if(old_form !== null){
                old_form.remove();
            }
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
                console.log(resp);
                if(parent.classList.contains('nodes')){
                    parent.innerHTML="";
                    getChildren(0);
                }
                else {
                    form.remove();
                    parent.querySelector('.plus').click();
                    parent.querySelector('.plus').click();
                }
            })
        }

        function del(e){
            e.preventDefault();
            let form = e.target;
            let formData = new FormData();
            formData.append('with',document.querySelector('.del_radio:checked').getAttribute('value'));
            for(let i=2;i<form.length;i++){
                formData.append(form[i].name,form[i].value);
            }
            let url = "{{route('delete_node')}}";
            fetch(url,{
                method:'post',
                body:formData
            }).then(resp => resp.json()).then(resp => {
                let parent = form.parentNode.parentNode;
                if(parent.classList.contains('nodes')){
                    parent.innerHTML="";
                    getChildren(0);
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
            let old_form = document.getElementById('container').querySelector('form');
            if(old_form !== null){
                old_form.remove();
            }
            form.setAttribute('method','post');
            let temp = window.location.href.split('/');
            let tree_id = temp[temp.length-1];
            form.innerHTML =
                '<div class="radio"><label for="option1"><input type="radio" class="del_radio" checked value="with" name="with" />Delete with children</label></div>' +
                '<div class="radio"><label for="option2"><input type="radio" class="del_radio" value="without" name="with" />' +
                'Add children to parent of removed node</label></div>' +
                '<input type="hidden" name="tree_id" value="'+tree_id+'"/>' +
                '<input type="hidden" name="id" value="'+id+'"/>' +
                '<button class="btn-danger" type="submit">Remove</button>';
            element.appendChild(form);
            form.addEventListener('submit',del);
        }

        function add_edit_form(e){
            let id = findClosestParentId(e.target)
            let element = document.getElementById(id);
            let form = document.createElement('form');
            let old_form = document.getElementById('container').querySelector('form');
            if(old_form !== null){
                old_form.remove();
            }
            form.setAttribute('method','post');
            let temp = window.location.href.split('/');
            let tree_id = temp[temp.length-1];
            form.innerHTML = '<label for="name">Name</label><input type="text" id="name" name="name" />' +
                '<input type="hidden" name="id" value="'+id+'">' +
                '<label for="parent_id">Parent ID</label><input type="number" name="parent_id"/> ' +
                '<input type="hidden" name="tree_id" value="'+tree_id+'"/>' +
                '<button type="submit">Add</button>';
            element.appendChild(form);
            form.addEventListener('submit',update);
        }
        function update(e){
            e.preventDefault();
            let form = e.target;
            let formData = new FormData();
            for(let i=0;i<form.length;i++){
                formData.append(form[i].name,form[i].value);
            }
            let url = "{{route('update_node')}}";
            fetch(url,{
                method:'post',
                body:formData
            }).then(resp => resp.json()).then(resp => {
                let parent = form.parentNode.parentNode;
                if(parent.classList.contains('nodes')){
                    parent.innerHTML="";
                    getChildren(0);
                }
                else {
                    form.remove();
                    parent.querySelector('.plus').click();
                    parent.querySelector('.plus').click();
                }
            })
        }
    </script>
    <div class="container" id="container">
        <div class="control row mt-1">

            @if($is_owner)<div class="col-md-3"><button id="add_mn_button" class="btn-secondary btn-success">Add main node</button></div>@endif
                <div class="col-md-3"><button id="sort_name_button" class="btn-secondary">
                        Sort by name</button></div>
            <div class="col-md-3"><button id="sort_id_button" class="btn-secondary">Sort by id </button></div>
        </div>
        <div class="nodes" id="nodes">
            @foreach($tree->root_nodes as $node)
                <div class="row mt-1">
                                    <div class="col-12">
                                            <div class="card node_body">
                                                    <div class="card-body pt-0 pb-0"><span  class="plus">v</span> {{$node->name}}
                                                            @if($is_owner)<div class="float-right"><button class="btn-success nod">Add child</button> <button class="btn-danger">
                                                                Delete node</button> <button class="btn-warning">Edit node</button></div>@endif</div>
                                                </div>
                                        </div>
                                </div>
            @endforeach
        </div>
    </div>
    <script>
        init_f();

    </script>
@endsection
