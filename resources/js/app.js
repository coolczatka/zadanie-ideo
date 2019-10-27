require('./bootstrap');

console.log("ello");

function add_plus_listeners() {
    $('.node').addEventListener('click',toggle)
}

function toggle() {
    alert(event.target.id);
}