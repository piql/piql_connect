<script>
    import { Line, mixins  } from 'vue-chartjs'
    const { reactiveProp } = mixins
    export default {
        extends: Line,
        mixins: [reactiveProp],
        data: () => ({
            chartdata: {
                labels: [],
                datasets: []
            },
            options: {
                maintainAspectRatio:false,
                scales: {
                    xAxes: [],
                    yAxes: [
                        {
                            ticks: {
                                beginAtZero: true
                            }
                        }
                    ]
                },
                legend: {
                    display: false
                },
                tooltips: {
                        mode: "index",
                        intersect: false
                },
                title: {
                    display: true,
                    fontFamily: "Agenda",
                    fontSize: 18,
                    fontColor: "#605f5f",
                    text: ""
                }
            }
        }),
        props: {
            url: "",
            title: "",
            labels: Array,
            chartData: Array,
        },
        methods: {
            last12Months: function (labels) {
                let currentMonth = new Date().getMonth() + 1;
                let rest = currentMonth % 12;
                let rotatedLabels = labels.slice(currentMonth,12).concat( labels.slice(0, rest) ) ;
                return rotatedLabels;
            },
        },
        async mounted () {
            this.chartdata.labels = this.last12Months( this.labels );
            let datasets = (await axios.get(this.url)).data;
            this.chartdata.datasets = datasets;
            this.options.title.text  = this.title;
            this.renderChart(this.chartdata, this.options)
        }
    }
</script>
