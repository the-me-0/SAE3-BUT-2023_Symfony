function openPopUp() {
    var element = document.getElementById("modal");
    element.classList.add("open");
}
function closePopUp() {
   var element = document.getElementById("modal");
   element.classList.remove("open");
}

function openUpdate(id) {
    var element = document.getElementById("updateModal");
    element.classList.add("open");
}
function closeUpdate(id) {
    var element = document.getElementById("updateModal");
    element.classList.remove("open");
}
function deleteFacility(id) {
    var deleteElement = document.getElementById('deleteUrl');
    deleteElement.href = '/batiment-' + id + '/supprimer-batiment';
}
function openDeleteRoom(id, batiment) {
    var element = document.getElementById("deleteModal");
    element.classList.add("open");
    var deleteElement = document.getElementById('deleteUrl');
    deleteElement.href = '/management/removeRoom/' + id + '/' + batiment;
}
function openDeleteSensor(id, batiment, room) {
    var element = document.getElementById("deleteModal");
    element.classList.add("open");
    var deleteElement = document.getElementById('deleteUrl');
    deleteElement.href = '/management/removeSensor/' + id + '/' + batiment + '/' + room;
}
function closeDelete() {
    var element = document.getElementById("deleteModal");
    element.classList.remove("open");
}

function succes(){
    let b = document.body;
    let newP = document.createElement ('p');
    newP. textContent = 'Vous avez créer le batiment avec succès';
    b. prepend (newP);
}