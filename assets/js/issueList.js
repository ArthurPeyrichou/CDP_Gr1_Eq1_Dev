import {showBarChart} from './graph';

const ChartNames = {
    STATUS: 'Status',
    DIFF: 'Difficulte',
    PRIO: 'Priorite'
};

const dataElement = document.getElementById('data');
const dataStatus = JSON.parse(dataElement.getAttribute('data-status'));
const dataDiff = JSON.parse(dataElement.getAttribute('data-diff'));
const dataPrio = JSON.parse(dataElement.getAttribute('data-prio'));

function showSpecificChart(name) {
    switch(name){
        case ChartNames.STATUS:
            showBarChart(dataStatus.title, dataStatus.datasetTitle, dataStatus.dataset);
            break;
        case ChartNames.DIFF:
            showBarChart(dataDiff.title, dataDiff.datasetTitle, dataDiff.dataset);
            break;
        case ChartNames.PRIO:
            showBarChart(dataPrio.title, dataPrio.datasetTitle, dataPrio.dataset);
            break;
        default:
            break;
    }
}

document.getElementById('select-chart').addEventListener('change',
    event => showSpecificChart(event.target.value)
);

showSpecificChart(ChartNames.STATUS);
