/*!
 * User Fingerprint Discuz! X plugin <https://github.com/ganlvtech/discuzx-user-fingerprint>
 * Copyright (C) 2018 Ganlv <https://github.com/ganlvtech>
 * License: GPL 3.0
 */
var el = document.getElementById('chart');

function resizeChart() {
    var width = el.parentElement.clientWidth;
    var height = window.innerHeight - el.parentElement.offsetTop - 20;
    if (width < 800) {
        width = 800;
    }
    if (height / width < 0.3) {
        height = width * 0.3;
    }
    el.style.width = width + 'px';
    el.style.height = height + 'px';
}

resizeChart();

var myChart = echarts.init(el);

window.onresize = function () {
    resizeChart();
    if (myChart) {
        myChart.resize();
    }
};


function handle(data) {
    myChart.hideLoading();

    var option = {
        title: data.title,
        legend: {
            data: data.categories
        },
        series: [{
            type: 'graph',
            layout: 'force',
            animation: false,
            draggable: true,
            roam: true,
            focusNodeAdjacency: true,
            force: {
                initLayout: 'circular',
                repulsion: 400,
                edgeLength: 100,
                gravity: 0.1
            },
            label: {
                position: 'bottom',
                formatter: '{b}'
            },
            itemStyle: {
                normal: {
                    borderColor: '#fff',
                    borderWidth: 1,
                    shadowBlur: 10,
                    shadowColor: 'rgba(0, 0, 0, 0.3)'
                }
            },
            lineStyle: {
                color: 'source'
            },
            emphasis: {
                lineStyle: {
                    width: 10
                }
            },
            data: data.nodes,
            categories: data.categories,
            edges: data.links
        }]
    };
    myChart.setOption(option);
}

myChart.showLoading();

handle(window.user_relation_data);

