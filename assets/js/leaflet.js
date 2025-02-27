document.addEventListener("DOMContentLoaded", function () {


    var map = L.map('map').setView([lat, lng], 13);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    var marker = L.marker([lat, lng]).addTo(map)
        .bindPopup(adresse)
        .openPopup();
});