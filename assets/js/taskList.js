import {showBarChart} from './graph';

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
    let ref = toMove.getAttribute('value');
    if(event.target.classList.contains('doing')){
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

document.querySelectorAll('li.todo, li.doing').forEach(
    element => element.addEventListener('dragstart', taskDragStart)
);

document.querySelectorAll('.dropping-card').forEach(
    element => {
        element.addEventListener('drop', taskDrop);
        element.addEventListener('dragover', taskAllowDrop);
    }
);

