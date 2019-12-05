function ajaxRequest(url) {
    const ajax = new XMLHttpRequest();
    ajax.open("GET", url, true);
    ajax.send();
}

function deleteItem(element){
    ajaxRequest(element.getAttribute("data-link"));
    let itemToRemove = document.getElementById("item-" + element.getAttribute("data-id"));
    itemToRemove.parentNode.removeChild(itemToRemove);
}

let classname = document.getElementsByClassName('delete-item');
for (var i = 0; i < classname.length; i++) {
    classname[i].addEventListener('click',
        event => deleteItem(event.target)
    );
}
