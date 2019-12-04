let chart = null;

/**
 * Shows a bar chart with one dataset.
 * @param title {string} The graph's title.
 * @param datasetTitle {string} The dataset's title.
 * @param dataset {{value, count}[]} The dataset to display.
 */
function showBarChart(title, datasetTitle, dataset){
    if (!isInitialized()) {
        initGraph();
    }

    const options = {
        ...getDefaultOptions(title, dataset.map(data => data.value)),
        xAxis: {
            data: dataset.map(data => data.value)
        },
        series: [{
            name: datasetTitle,
            type: 'bar',
            data: dataset.map(data => data.count)
        }]
    };
    showGraphWithOptions(options);
}

/**
 * Shows an area chart with two dataset.
 * @param title {string} The graph's title.
 * @param firstTitle {string} The first dataset's title.
 * @param secondTitle {string} The second dataset's title.
 * @param firstDataset {{value, count}[]} The first dataset to display.
 * @param secondDataset {{value, count}[]} The second dataset to display.
 */
function showBiDatasetAreaChart(title, firstTitle, secondTitle,
    firstDataset, secondDataset){
    if (!isInitialized()) {
        initGraph();
    }

    const options = {
        ...getDefaultOptions(title, [firstTitle, secondTitle]),
        tooltip : {
            trigger: 'axis',
            axisPointer: {
                type: 'cross',
                label: {
                    backgroundColor: '#6a7985'
                }
            }
        },
        xAxis : [
            {
                type : 'category',
                boundaryGap : false,
                data : firstDataset.map(data => data.value)
            }
        ],
        series : [
            {
                name: firstTitle,
                type:'line',
                stack: '',
                areaStyle: {},
                data: firstDataset.map(data => data.count)
            },
            {
                name: secondTitle,
                type:'line',
                stack: '',
                areaStyle: {},
                data: secondDataset.map(data => data.count)
            }
        ]
    };
    showGraphWithOptions(options);
}

function getDefaultOptions(title, legend) {
    return {
        title: {
            text: title
        },
        tooltip : {},
        legend: {
            data: legend
        },
        toolbox: {
            feature: {
                saveAsImage: {}
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis : [],
        yAxis : [
            {
                type : 'value'
            }
        ],
        series : []
    };
}

function initGraph() {
    chart = echarts.init(document.getElementById('chart'));
}

function isInitialized() {
    return chart !== null;
}

function showGraphWithOptions(options) {
    chart.setOption(options);
}

export {
    showBarChart,
    showBiDatasetAreaChart
}
