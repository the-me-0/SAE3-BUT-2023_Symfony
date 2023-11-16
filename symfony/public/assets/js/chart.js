let ctx;
let tempObjective;
let humObjective;
let co2Objective;
let roomNum;
let chart;
let dataType = '';
let intervalId = null;
let dataDuration;
let tempId;

// Quand le DOM est chargé, crée le graphique et récupère les données
document.addEventListener('DOMContentLoaded', function() {
    // Récupère le contexte de canvas et les données de graphique à partir du DOM
    ctx = document.getElementById('myChart').getContext('2d');
    tempObjective = document.getElementById('temp_objective').textContent;
    humObjective = document.getElementById('hum_objective').textContent;
    co2Objective = document.getElementById('co2_objective').textContent;
    roomNum = document.getElementById('room_id').textContent;
    dataType = 'temp';
    dataDuration = 'day';
    tempId = 4;

    getAllData(dataType, (chartData) => {
        chart = new Chart(ctx,
            {
                type: 'line',
                data: chartData,
                options: {
                    scales: {
                        x: {
                            type: 'time',
                            time: {
                                unit: 'hour',
                                parser: 'HH:mm'
                            },
                        },
                    },
                    tension: 0.1,
                    animation:{
                        duration: 0
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { boxWidth: 1 }
                    }
                }
            });
        intervalId = setInterval(() => getData(dataType), 60000);
    });

    colorTempButton(4);
    colorTypeButton(2);
});

// Fonction pour récupérer et formater les données pour le graphique
function getAllData(type, callback, duration = dataDuration) {
    console.log('Getting data ...');
    // Récupère les données depuis le serveur
    fetch(`/room/data/${roomNum}`)
        .then(response => response.json())
        .then(data => {
            // Prépare les données de graphique
            const chartData = {
                labels: [],
                datasets: [{
                    data: [],
                    borderColor: '#386CD9',
                    fill: false,
                    pointRadius: 3
                }]
            };

            // Formate les données pour le graphique
            const now = new Date();
            let dataDuration;

            switch (duration)
            {
                case "day":
                    if (chart)
                        chart.options.scales.x.time.unit = "hour";
                    dataDuration = new Date(new Date().getTime() - (24 * 60 * 60 * 1000));
                    for (const point of data.allData) {
                        let date = new Date(point.date);
                        if (date >= dataDuration) {
                            if (point[type] !== undefined)
                                chartData.datasets[0].data.push({x: date, y: point[type]});
                        }
                    }
                    break;
                case "week":
                    if (chart)
                        chart.options.scales.x.time.unit = "day";
                    dataDuration = new Date(new Date().getTime() - (7 * 24 * 60 * 60 * 1000));
                    for (const point of getDataByDay(data.allData)) {
                        let date = new Date(point.x);
                        console.log('Date : ', date);
                        if (date >= dataDuration) {
                            chartData.datasets[0].data.push(point);
                        }
                    }
                    break;
                case "month":
                    if (chart)
                        chart.options.scales.x.time.unit = "day";
                    dataDuration = new Date(new Date().getTime() - (30 * 24 * 60 * 60 * 1000));
                    for (const point of getDataByDay(data.allData)) {
                        let date = new Date(point.x);
                        console.log('Date : ', date);
                        if (date >= dataDuration) {
                            chartData.datasets[0].data.push(point);
                        }
                    }
                    break;
            }

            if (chartData.datasets[0].data.length === 0) {
                document.getElementById("myChart").style.display = "none";
                let message = document.createElement("div");
                message.id = "message";
                message.innerHTML = "Aucune donnée pour tracer un graphique";
                document.getElementById("chart_Container").appendChild(message);
            } else {
                if (document.getElementById("message")) {
                    document.getElementById("message").remove();
                }
                document.getElementById("myChart").style.display = "block";
            }

            console.log(duration);
            console.log(dataDuration);

            switch (dataType) {
                case 'temp':
                    chartData.datasets[0].label = 'Température';
                    break;
                case 'hum':
                    chartData.datasets[0].label = 'Humidité';
                    break;
                case 'co2':
                    chartData.datasets[0].label = 'CO2';
                    break;
            }
            callback(chartData);
        });
}

// Fonction pour changer le type de données affichées sur le graphique
function getData(type, id) {
    clearInterval(intervalId);  // arrêter l'intervalle en cours avant de créer un nouveau
    dataType = type;
    getAllData(type, (chartData) => {
        chart.data.labels = chartData.labels;
        chart.data.datasets = chartData.datasets;
        chart.update();
        intervalId = setInterval(() => getData(dataType), 60000);  // réinitialiser l'intervalle avec les nouvelles données
    });

    if (id < 4)
        colorTypeButton(id);
    else
        colorTempButton(id);
}

function filterData(id, duration){
    tempId = id;
    dataDuration = duration;
    clearInterval(intervalId);  // arrêter l'intervalle en cours avant de créer un nouveau
    getAllData(dataType, (chartData) => {
        chart.data.labels = chartData.labels;
        chart.data.datasets = chartData.datasets;
        chart.update();
        intervalId = setInterval(() => getData(dataType), 60000);  // réinitialiser l'intervalle avec les nouvelles données
    }, dataDuration);
    colorTempButton(tempId);
}

function colorTypeButton(id){
    for (let i = 1; i < 4 ; i++)
    {
        let button = document.getElementById(i.toString());
        button.style.background = '#FFF';
        button.children[0].style.color = '#000';
    }
    document.getElementById(id).style.background = 'rgb(56, 108, 217)';
    document.getElementById(id).children[0].style.color = '#FFF';
}

function colorTempButton(id){
    for (let i = 4; i < 7 ; i++)
    {
        let button = document.getElementById(i.toString());
        button.style.background = '#FFF';
        button.children[0].style.color = '#000';
    }
    document.getElementById(tempId).style.background = 'rgb(56, 108, 217)';
    document.getElementById(tempId).children[0].style.color = '#FFF';
}

function getDataByDay(rawData) {
    const dataByDay = rawData.reduce((acc, curr) => {
        const date = new Date(curr.date);
        const day = date.toDateString();
        if (!acc[day]) {
            acc[day] = {
                day: day,
                data: []
            };
        }
        if(curr[dataType] !== undefined)
            acc[day].data.push(curr[dataType]);
        return acc;
    }, {});

    const data = Object.values(dataByDay);
    const avgData = data.map(d => {
        if(d.data && d.data.length){
            const sum = d.data.reduce((a, b) => a + b,0);
            return {
                x: new Date(d.day),
                y: sum / d.data.length
            };
        }
    });
    return avgData.filter(d=>d);
}





