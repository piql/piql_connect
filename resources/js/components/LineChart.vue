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
                    xAxes: [{
                        scaleLabel: {
                            display: false,
                            fontFamily: "Agenda",
                            fontSize: 14,
                            fontColor: "#605f5f",
                            labelString: ""
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        },
                        scaleLabel: {
                            display: false,
                            fontFamily: "Agenda",
                            fontSize: 14,
                            fontColor: "#605f5f",
                            labelString: ""
                        }

                    }]
                },
                legend: {
                    display: false
                },
                tooltips: {
                        mode: "index",
                        intersect: false
                },
                title: {
                    display: false,
                    fontFamily: "Agenda",
                    fontSize: 18,
                    fontColor: "#605f5f",
                    text: ""
                },

            }
        }),
        props: {
            url: "",
            title: "",
            xLabel: "",
            yLabel: "",
            labels: Array,
            chartData: Array,
        },
        methods: {
            last12Months: function (labels) {
                let currentMonth = new Date().getMonth() + 1;
                let rest = currentMonth % 12;
                let rotatedLabels = labels.slice(rest,12).concat( labels.slice(0, rest) ) ;
                return rotatedLabels;
            },
        },
        async mounted () {
            this.chartdata.labels = this.last12Months( this.labels );
            let datasets = (await axios.get(this.url)).data;
            this.chartdata.datasets = datasets;
            if(this.title) {
                this.options.title.display  = true;
                this.options.title.text  = this.title;
            }
            if(this.xLabel) {
                this.options.scales.xAxes[0].scaleLabel.display = true;
                this.options.scales.xAxes[0].scaleLabel.labelString = this.xLabel;
            }
            if(this.yLabel) {
                this.options.scales.yAxes[0].scaleLabel.display = true;
                this.options.scales.yAxes[0].scaleLabel.labelString = this.yLabel;
            }
            this.renderChart(this.chartdata, this.options)
        }
    }
</script>
