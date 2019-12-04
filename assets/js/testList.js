import {showBarChart} from './graph';

const data = JSON.parse(document.getElementById('data').getAttribute('data-status'));

showBarChart(data.title, data.datasetTitle, data.dataset);
