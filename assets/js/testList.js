import {showBarChart} from './graph';
import {itemDragStart, itemDrop, itemAllowDrop} from './dragDrop';

const data = JSON.parse(document.getElementById('data').getAttribute('data-status'));

showBarChart(data.title, data.datasetTitle, data.dataset);

document.querySelectorAll('li.todo, li.succeeded, li.failed').forEach(
    element => element.addEventListener('dragstart', itemDragStart)
);

document.querySelectorAll('.dropping-card').forEach(
    element => {
        element.addEventListener('drop', itemDrop);
        element.addEventListener('dragover', itemAllowDrop);
    }
);
