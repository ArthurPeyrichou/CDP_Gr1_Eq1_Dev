import {showBiDatasetAreaChart} from './graph';

const data = JSON.parse(document.getElementById('data').getAttribute('data-bdc'));

showBiDatasetAreaChart(data.title, data.firstTitle, data.secondTitle, data.firstDataset, data.secondDataset);
