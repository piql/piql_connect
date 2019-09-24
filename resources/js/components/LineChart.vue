<script>
    import { Line, mixins  } from 'vue-chartjs'
    const { reactiveProp } = mixins
    export default {
        extends: Line,
        mixins: [reactiveProp],
        data: () => ({
            chartdata: {
                labels: ["Oct","Nov","Dec","Jan","Feb","Mar","Apr","May","Jun","Jul ","Aug","Sep"],
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
