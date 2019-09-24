<script>
    import { Pie, mixins  } from 'vue-chartjs'
    const { reactiveProp } = mixins
    export default {
        extends: Pie,
        mixins: [reactiveProp],
        data: () => ({
            labels: [],
            chartdata: [],
            options: {
                maintainAspectRatio:false,
                scales: {
                    xAxes:[],
                    yAxes:{
                    0:{
                        ticks:{
                            beginAtZero:true
                            }
                        },display:false
                    }
                },
                legend:{
                    display:true,
                    position:"right"
                },
                tooltips: {
                    mode:"point",
                    intersect:false
                },
                title:{
                    display:true,
                    fontFamily:"Agenda",
                    fontSize:18,
                    fontColor:"#605f5f",
                    text:""
                },
                animation:{
                    animateRotate:true,
                    animateScale:true
                }
            }
        }),
        props: {
            chartData: [],
            url: "",
            title: ""
        },
        async mounted () {
            let datasets = (await axios.get(this.url)).data;
            this.chartdata.datasets = datasets;
            this.options.title.text  = this.title;
            this.renderChart(this.chartdata, this.options)
        }
    }
</script>
