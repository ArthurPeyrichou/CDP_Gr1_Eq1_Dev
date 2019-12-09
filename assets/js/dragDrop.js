function itemDragStart(event) {
    event.dataTransfer.setData('text/plain', event.target.id);
}

function itemAllowDrop(event) {
    event.preventDefault();
}

//On drop we change status if task and state if test
function itemDrop(event) {
    event.preventDefault();
    const data = event.dataTransfer.getData('text/plain');
    const toMove = document.getElementById(data);
    let ref = toMove.getAttribute('data-link');
    if(event.target.classList.contains('failed')){
        ref = ref.replace('todo','failed');
    } else if(event.target.classList.contains('succeeded')){
        ref = ref.replace('todo','succeeded');
    } else if(event.target.classList.contains('doing')){
        ref = ref.replace('done','doing');
    }

    fetch(ref).then(
        response => {
            if (response.ok) {
                event.target.children[1].children[0].appendChild(toMove);
            }
        }
    );
}

export {
    itemDragStart,
    itemAllowDrop,
    itemDrop
}
