// PATH: assets/js/dashboard.js

/*
* This js code will reload the dashboard every minute
*/


// execute reloadDashboard when the page is loaded
window.onload = function () {
    reloadDashboard("http://localhost:9979/dashboard/reload");
};


function reloadDashboard(url) {

    console.log("Reloading dashboard...");

    let results = fetch(url);

    results.then((response) => {

        return response.json();
        
    }).then((data) => {

        console.log(data);

        refreshPageData(data);

        setTimeout(function () {
            reloadDashboard(url);
        }, 60000);

    });
}


function refreshPageData(data) {
    // refresh the page data
    document.getElementById("rooms_in_alert").innerHTML = data.rooms_in_alert;
    document.getElementById("acquisition_systems_not_connected").innerHTML = data.acquisition_systems_not_connected;
    document.getElementById("rooms_without_acquisition_system").innerHTML = data.rooms_without_acquisition_system;
}