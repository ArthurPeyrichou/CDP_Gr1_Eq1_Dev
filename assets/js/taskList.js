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


function dragStart(event) {
    event.dataTransfer.setData("Text", event.target.id);
}

function dragging(event) {

}

function allowDrop(event) {
    event.preventDefault();
}

function drag(ev) {
    ev.dataTransfer.setData("text", ev.target.id);
}

function drop(ev) {
    ev.preventDefault();
    const data = ev.dataTransfer.getData("Text");
    ev.target.children[1].children[0].appendChild(document.getElementById(data));
    let ref = document.getElementById(data).getAttribute('value');
    if(ev.target.getAttribute('class').includes('doing')){
        ref = ref.replace('done','doing');
    }

    const ajax = new XMLHttpRequest();
    ajax.open("GET", ref, true);
    ajax.send();
}
