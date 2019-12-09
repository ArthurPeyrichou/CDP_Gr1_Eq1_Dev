import {showBarChart} from './graph';
import {itemDragStart, itemDrop, itemAllowDrop} from './dragDrop';

const ChartNames = {
    STATUS: 'Status',
    MAN_DAYS: 'JH',
    MEMBER: 'Membre',
    MAN_DAYS_MEMBER: 'JHParMembre'
};

const dataElement = document.getElementById('data');
const dataStatus = JSON.parse(dataElement.getAttribute('data-status'));
const dataManDays = JSON.parse(dataElement.getAttribute('data-manDays'));
const dataMember = JSON.parse(dataElement.getAttribute('data-member'));
const dataManDaysMember = JSON.parse(dataElement.getAttribute('data-manDaysMember'));



function showSpecificChart(name) {
    switch(name){
        case ChartNames.STATUS:
            showBarChart(dataStatus.title, dataStatus.datasetTitle, dataStatus.dataset);
            break;
        case ChartNames.MAN_DAYS:
            showBarChart(dataManDays.title, dataManDays.datasetTitle, dataManDays.dataset);
            break;
        case ChartNames.MEMBER:
            showBarChart(dataMember.title, dataMember.datasetTitle, dataMember.dataset);
            break;
        case ChartNames.MAN_DAYS_MEMBER:
            showBarChart(dataManDaysMember.title, dataManDaysMember.datasetTitle, dataManDaysMember.dataset);
            break;
        default:
            break;
    }
}

document.getElementById('select-chart').addEventListener('change',
    event => showSpecificChart(event.target.value)
);

showSpecificChart(ChartNames.STATUS);

document.querySelectorAll('li.todo, li.doing').forEach(
    element => element.addEventListener('dragstart', itemDragStart)
);

document.querySelectorAll('.dropping-card').forEach(
    element => {
        element.addEventListener('drop', itemDrop);
        element.addEventListener('dragover', itemAllowDrop);
    }
);

