function getDataFacility(){
    var name = document.getElementById("name").value;
    var sector = document.getElementById("sector").value;
    var number = document.getElementById("number").value;
    var street = document.getElementById("street").value;
    var city = document.getElementById("city").value;
    var postalCode = document.getElementById("postalCode").value;
    var country = document.getElementById("country").value;

    let url = "/management/createFacility/" + name + "/" + sector + "/" + number + "/" + street + "/" + city + "/" + postalCode + "/" + country;

    document.location.href=url;
}

