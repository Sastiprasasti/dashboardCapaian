let petugasChart;
let progressChart;

document.addEventListener('DOMContentLoaded', () => {

    initCharts();

    loadDashboard();

    document
        .getElementById('kecamatan')
        ?.addEventListener('change', loadDashboard);

});

function loadDashboard()
{
    const kecamatan =
        document.getElementById('kecamatan').value;

    fetch(`/dashboard/data?kecamatan=${kecamatan}`)
        .then(response => response.json())
        .then(data => {

            renderKPI(data);

            renderPetugasChart(data);

            renderProgressChart(data);

            renderRanking(data);

        });
}