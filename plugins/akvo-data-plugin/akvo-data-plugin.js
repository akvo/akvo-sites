    var chart=[];
    var chartData = [];
jQuery(document).ready(function() {
        
       jQuery('.chart').each(function(index){
           generateChart(jQuery(this).attr('type'),jQuery(this).attr('id'));
       });


});

function generateChart(type,container){
    Highcharts.setOptions({
        chart: {
            style: {
                fontFamily: 'Arial, Verdana, Helvetica, sans-serif', // default font
                fontSize: '10px',
                fontColor:'#4a4a4a',
                fontWeight: 'bold'
            }
        }
    });
    
    //get chart data

    chartData[container] = getChartData(container);
    var date = new Date();
    //var callback = function(){};
    var chartOptions = {
        chart: {
            renderTo: container,
            backgroundColor: '#eaeaea',
            plotBorderWidth: null,
            plotShadow: false,
            type:type,
            height:200,
            style:{
                    color:'#4a4a4a',
                    zIndex:'1'
                }

        },
       
        title: {
            text: chartData[container].title,
            style:{
                fontSize: '13px',
                color:'#4a4a4a',
                fontWeight:'bold'
            },
			align:'right',
			floating: true,
            x: -10.0
        },
        tooltip: {
                formatter: function() {
                        return '<b>'+ this.series.name +'</b><br/>'+
                        this.x +': '+ Highcharts.numberFormat(this.y,0,',','.') +'';
                }
            },
        credits: {
            enabled: false
        },
        plotOptions: {
            line:{
                connectNulls:true
            }
        },
        xAxis: jQuery.extend(true,chartData[container].xaxis,{
                labels:{
                    style:{
                        color:'#4a4a4a'
                    },
                    formatter:function(){
                        return this.value.substr(0,1);
                    }
                },
                plotLines: [{
                    color: '#000',
                    width: 1,
                    value: parseInt(chartData[container].yearPlot),
                    label:{
                        text: date.getFullYear(),
                        rotation:0,
                        style:{
                            color:'#4a4a4a',
                            fontSize:'8px'
                        },
                        y:130
                    },
                    dashStyle:'longdash'
                   
                }]
            }),
         yAxis:{
             title:{
                 text:null
             }
         },
        
         legend: {
                enabled:chartData[container].showLegend,
                align:'center',
                borderWidth:0,
                itemStyle:{
                    color:'#4a4a4a'
                }
            },
        series: chartData[container].series
    }

    
    jQuery('#'+container).show();
    // Build the chart
    chart[container] = new Highcharts.Chart(chartOptions,function(chart){
        

    });

    //append legend



}

function getChartData(container){
    //render container inner html into chart values
    var chartData = {};
    if(jQuery('#'+container+' div.chart-legend').html()=='true'){
        chartData.showLegend = true;
    }else{
        chartData.showLegend = false;
    }
    chartData.title = jQuery('#'+container+' div.chart-title').html();
    chartData.yearPlot = jQuery('#'+container+' div.chart-year').html();

    if(jQuery('#'+container+' div.chart-xaxis').length>0){
        chartData.xaxis = {};
        chartData.xaxis.categories = [];
        jQuery('#'+container+' div.chart-xaxis div').each(function(i){
                chartData.xaxis.categories.push(jQuery(this).html());
        });

    }
    if(jQuery('#'+container+' div.chart-yaxis').length>0){
        chartData.yaxis = [];
        jQuery('#'+container+' div.chart-yaxis > div').each(function(i){
                var yaxisdata = {};
                yaxisdata.title = {
                    text : jQuery(this).find('div.label').html()
                };
                if(i>0)yaxisdata.opposite = true;

                chartData.yaxis.push(yaxisdata);
        });

    }
    chartData.series = [];
    jQuery('#'+container+' div.chart-serie').each(function(i){
        var newSerie = {};
        newSerie.name = jQuery(this).attr('name');
        if(jQuery(this).attr('color')) newSerie.color = jQuery(this).attr('color');
        if(jQuery(this).attr('type')) newSerie.type = jQuery(this).attr('type');
        if(jQuery(this).attr('yaxis')) newSerie.yAxis = parseInt(jQuery(this).attr('yaxis'));
        var datalist = [];
        jQuery(this).children('div').each(function(c){
            //pie data has a name, other charts data don't have a name
            var dataobject = {};

            if(jQuery(this).attr('name'))dataobject.name = jQuery(this).attr('name');
            if(jQuery(this).attr('color'))dataobject.color = jQuery(this).attr('color');
            dataobject.visible = true;
            
            if(jQuery(this).html()==''){
                dataobject.y = null;
            }else{
                var datalabel = parseInt(jQuery(this).html().replace(',','.'));
                dataobject.y = datalabel;
            } 

            datalist.push(dataobject);
        });
        newSerie.data = datalist;

        chartData.series.push(newSerie);
    });
    return chartData;
}

