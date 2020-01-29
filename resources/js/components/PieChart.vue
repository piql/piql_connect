<script>
    import { Pie, mixins  } from 'vue-chartjs'
    const { reactiveProp } = mixins
    export default {
        extends: Pie,
        mixins: [reactiveProp],
        data: () => ({
            datasets: [
                {
                    backgroundColor: [
                        '#6f3c2a',
                        '#473181',
                        '#389451',
                        '#a63f5b',
                        '#aa405d',
                        '#b94665',
                        '#c05974',
                        '#c76b84',
                        '#ce7e93',
                        '#d590a2',
                        '#dca3b2',
                        '#e3b5c1',
                        '#eac8d1',
                        '#f1dae0',
                        '#f8edf0',
                        '#ffffff',
                    ],
                    data: [],
                }
            ],
            labels: [],
            options: {
                maintainAspectRatio: true,
                scales: {
                    xAxes:[],
                    yAxes:{
                    0: {
                        ticks: {
                            beginAtZero: true
                        }
                    },
                        display:false
                    }
                },
                legend:{
                    display: true,
                    position: 'bottom'
                },
                tooltips: {
                    mode:"point",
                    intersect:false
                },
                title:{
                    display: true,
                    fontFamily:"Agenda",
                    fontSize: 18,
                    fontColor: "#605f5f",
                    text:"Hei"
                },
                animation:{
                    animateRotate:true,
                    animateScale:false
                }
            }
        }),
        props: {
            chartData: [],
            url: "",
            title: ""
        },
        async mounted () {
            axios.get(this.url).then( (data) => {
                let result = data.data;
                let labels = Object.keys(result);
                this.datasets[0].data =  Object.values(result);
                this.options.title.text  = this.title;
                this.renderChart({'datasets': this.datasets, 'labels': labels}, this.options);
            });
        }
    }
</script>
