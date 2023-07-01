<script>
const bestSellingData = <?php echo $json_best_selling_data; ?>;

const doughnutChartLabels = bestSellingData.map(item => `${item.product_name} `);
const doughnutChartData = bestSellingData.map(item => item.total_price);
const backgroundColors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#E7E9ED'];

const doughnutChartConfig = {
    type: 'doughnut',
    data: {
        labels: doughnutChartLabels,
        datasets: [{
            data: doughnutChartData,
            backgroundColor: backgroundColors
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            title: {
                display: true,
                text: 'Best selling products'
            },
            legend: {
                position: 'right',
            }
        }
    }
};

const doughnutChartCtx = document.getElementById('doughnutChart').getContext('2d');
new Chart(doughnutChartCtx, doughnutChartConfig);
</script>