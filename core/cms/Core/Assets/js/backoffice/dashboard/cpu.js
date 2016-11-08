/*
 +------------------------------------------------------------------------+
 | PhalconEye CMS                                                         |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2016 PhalconEye Team (http://phalconeye.com/)       |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file LICENSE.txt.                             |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconeye.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
 | Author: Ivan Vorontsov <lantian.ivan@gmail.com>                 |
 +------------------------------------------------------------------------+
 */

/**
 * Dashboard CPU chart.
 *
 * @category  PhalconEye
 * @package   PhalconEye Core Module
 * @author    Ivan Vorontsov <lantian.ivan@gmail.com>
 * @copyright Copyright (c) 2013-2016 PhalconEye Team
 * @license   New BSD License
 * @link      http://phalconeye.com/
 */

$(function () {
    Highcharts.setOptions({
        global: {
            useUTC: false
        }
    });

    // Create the chart
    Highcharts.stockChart('monitoring-cpu', {
        chart: {
            events: {
                load: function () {
                    var $this = this;
                    var series = this.series[0];
                    $('.dashboard').on('monitoring', function (e, data) {
                        var x = (new Date()).getTime();
                        series.addPoint([x, data.cpu]);
                        $this.setTitle({text: "CPU (" + data.cpu + " %)"});
                    });
                }
            }
        },

        rangeSelector: {
            buttons: [{
                count: 1,
                type: 'minute',
                text: '1M'
            }, {
                count: 5,
                type: 'minute',
                text: '5M'
            }, {
                count: 10,
                type: 'minute',
                text: '10M'
            }, {
                count: 30,
                type: 'minute',
                text: '30M'
            }, {
                count: 60,
                type: 'minute',
                text: '60M'
            }, {
                type: 'all',
                text: 'All'
            }],
            inputEnabled: false,
            selected: 0
        },

        title: {
            text: 'CPU'
        },
        yAxis: {
            labels: {
                formatter: function() {
                    return this.value + ' %';
                }
            },
        },
        series: [{
            name: 'CPU',
            type: 'area',
            fillColor: {
                linearGradient: {
                    x1: 0,
                    y1: 0,
                    x2: 0,
                    y2: 1
                },
                stops: [
                    [0, Highcharts.getOptions().colors[0]],
                    [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                ]
            },
            threshold: null,
            data: []
        }]
    });

});

