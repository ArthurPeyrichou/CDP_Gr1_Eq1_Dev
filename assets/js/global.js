function ajaxRequest(url) {
    const ajax = new XMLHttpRequest();
    ajax.open("GET", url, true);
    ajax.send();
}

function deleteItem(element){
    
    fetch(element.getAttribute("data-link")).then(
        response => {
            if (response.ok) {
                let itemToRemove = document.getElementById("item-" + element.getAttribute("data-id"));
                itemToRemove.parentNode.removeChild(itemToRemove);
            }
        }
    );
}

document.querySelectorAll('button.delete-item').forEach(
    element => element.addEventListener('click', event => deleteItem(event.target))
);
