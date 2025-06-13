document.addEventListener("DOMContentLoaded", () => {
    const map = L.map('map').setView([34.101558, -118.342345], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);
    L.marker([34.101558, -118.342345]).addTo(map)
        .bindPopup('7060 Hollywood Blvd, Los Angeles, CA')
        .openPopup();
})