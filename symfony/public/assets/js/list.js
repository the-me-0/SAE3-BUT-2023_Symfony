


function openPopUpSensor(id) {
    var element = document.getElementById("modal");
    var title = document.getElementById("popUpTitle");
    var row = document.getElementById("sensorRow"+id);

    title.innerHTML = "Informations capteur "+id

    document.getElementById("roomEl").innerHTML = row.children[1].innerHTML;
    document.getElementById("numEl").innerHTML = row.children[2].innerHTML;
    document.getElementById("enabledEl").innerHTML = row.children[3].innerHTML;
    document.getElementById("descEl").innerHTML = row.children[4].innerHTML;

    element.classList.add("open");
}

function openPopUpRoom(id) {
    var element = document.getElementById("modal");
    var title = document.getElementById("popUpTitle");
    var row = document.getElementById("roomRow"+id);

    title.innerHTML = "Informations salle "+id

    document.getElementById("numEl").innerHTML = row.children[1].innerHTML;
    document.getElementById("surfaceEl").innerHTML = row.children[2].innerHTML;
    document.getElementById("windowsEl").innerHTML = row.children[3].innerHTML;

    /* AJAX Request */
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            makeHtmlSensor(document.getElementById("redirectionSide"), JSON.parse(this.responseText));
        }
    };
    const url='http://localhost:9979/ajax/room-'+id+'/sensors';
    xhttp.open("GET", url);
    xhttp.send();

    element.classList.add("open");
}

function openPopUpFacility(id) {
    var element = document.getElementById("modal");
    var title = document.getElementById("popUpTitle");
    var row = document.getElementById("facilityRow"+id);

    title.innerHTML = "Informations bâtiment "+id

    document.getElementById("nameEl").innerHTML = row.children[1].innerHTML;
    document.getElementById("sectorEl").innerHTML = row.children[2].innerHTML;

    /* AJAX Request */
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            makeHtmlRoom(document.getElementById("redirectionSide"), JSON.parse(this.responseText));
        }
    };
    const url='http://localhost:9979/ajax/facility-'+id+'/rooms';
    xhttp.open("GET", url);
    xhttp.send();
    

    element.classList.add("open");
}


function makeHtmlRoom(parentElement, json) {
    try {
        var global = document.createElement("div");
        global.className = "viewer_content";
        global.id = "popUpViewerContent";

        /* going through rooms to display them in global var */
        for(room of json) {
            var global_a = document.createElement("a"); global_a.href="/management/facility"+room["fid"]+"/room"+room["id"];
            var global_div = document.createElement("div"); global_div.className = "facility";
            var id_div = document.createElement("div"); id_div.className = "id";
            var id_div_label = document.createElement("h6"); id_div_label.innerHTML = "ID";
            var id_div_value = document.createElement("h6"); id_div_value.innerHTML = room["id"];
            id_div.appendChild(id_div_label); id_div.appendChild(id_div_value);
            global_div.appendChild(id_div);
            var info_div = document.createElement("div"); info_div.className="info";
            var info_div_value = document.createElement("h6"); info_div_value.innerHTML=room["num"];
            info_div.appendChild(info_div_value);
            global_div.appendChild(info_div);
            global_a.appendChild(global_div);
            global.appendChild(global_a);
        }

        /* if no room were added in for loop */
        if(global.children == null) {
            global.innerHTML = "Pas de salle à afficher.";
        }
        parentElement.appendChild(global);

    } catch(e) {
        parentElement.innerHTML = "JS error : bad argument given at makeHtmlRoom";
    }
}

function makeHtmlSensor(parentElement, json) {
    try {
        var global = document.createElement("div");
        global.className = "viewer_content";
        global.id = "popUpViewerContent";

        /* going through rooms to display them in global var */
        for(sensor of json) {
            var global_a = document.createElement("a"); global_a.href="#";
            var global_div = document.createElement("div"); global_div.className = "facility";
            var id_div = document.createElement("div"); id_div.className = "id";
            var id_div_label = document.createElement("h6"); id_div_label.innerHTML = "ID";
            var id_div_value = document.createElement("h6"); id_div_value.innerHTML = sensor["id"];
            id_div.appendChild(id_div_label); id_div.appendChild(id_div_value);
            global_div.appendChild(id_div);
            var info_div = document.createElement("div"); info_div.className="info";
            var info_div_num = document.createElement("h6"); info_div_num.innerHTML=sensor["num"];
            var info_div_type = document.createElement("h6"); info_div_type.className = "smaller"; info_div_type.innerHTML="type à définir";
            var info_div_status = document.createElement("h6"); info_div_status.className = "small"; info_div_status.innerHTML="Status : "+sensor["enabled"];
            if(sensor["enabled"]=="OK") {
                var info_div_status_logo = document.createElement("div"); info_div_status_logo.className="status"; info_div_status_logo.id="ok";
            } else {
                var info_div_status_logo = document.createElement("div"); info_div_status_logo.className="status"; info_div_status_logo.id="ko";
            }
            info_div_status.appendChild(info_div_status_logo);
            info_div.appendChild(info_div_num);
            info_div.appendChild(info_div_type);
            info_div.appendChild(info_div_status);
            global_div.appendChild(info_div);
            global_a.appendChild(global_div);
            global.appendChild(global_a);
        }

        /* if no room were added in for loop */
        if(global.children == null) {
            global.innerHTML = "Pas de salle à afficher.";
        }
        parentElement.appendChild(global);

    } catch(e) {
        parentElement.innerHTML = "JS error : bad argument given at makeHtmlRoom";
    }
}


function closePopUp() {
    var element = document.getElementById("modal");
    var viewerContent = document.getElementById("popUpViewerContent");

    if(viewerContent != null) { /* counters the fact that sensor page hasn't an iframe */
        document.getElementById("redirectionSide").removeChild(viewerContent);
    }
    element.classList.remove("open");
}