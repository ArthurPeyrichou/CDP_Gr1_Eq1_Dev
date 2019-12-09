import {showBarChart} from './graph';

const data = JSON.parse(document.getElementById('data').getAttribute('data-status'));

showBarChart(data.title, data.datasetTitle, data.dataset);

function taskDragStart(event) {
    event.dataTransfer.setData('text/plain', event.target.id);
}

function taskAllowDrop(event) {
    event.preventDefault();
}

function taskDrop(event) {
    event.preventDefault();
    const data = event.dataTransfer.getData('text/plain');
    const toMove = document.getElementById(data);
    let ref = toMove.getAttribute('data-link');
    if(event.target.classList.contains('failed')){
        ref = ref.replace('todo','failed');
    } else if(event.target.classList.contains('succeeded')){
        ref = ref.replace('todo','succeeded');
    }

    fetch(ref).then(
        response => {
            if (response.ok) {
                event.target.children[1].children[0].appendChild(toMove);
            }
        }
    );
}

document.querySelectorAll('li.todo, li.succeeded, li.failed').forEach(
    element => element.addEventListener('dragstart', taskDragStart)
);

document.querySelectorAll('.dropping-card').forEach(
    element => {
        element.addEventListener('drop', taskDrop);
        element.addEventListener('dragover', taskAllowDrop);
    }
);

