document.addEventListener("DOMContentLoaded", () => {
    const inputAutocomplete = document.getElementById("lieu_autocomplete");
    const selectAdresse = document.createElement("div");
    selectAdresse.id = "adresse-results";
    selectAdresse.style.display = "none";
    selectAdresse.style.position = "absolute";
    selectAdresse.style.backgroundColor = "white";
    selectAdresse.style.border = "1px solid #ccc";
    selectAdresse.style.boxShadow = "0 2px 5px rgba(0, 0, 0, 0.1)";
    selectAdresse.style.width = inputAutocomplete.offsetWidth + "px";
    inputAutocomplete.parentNode.appendChild(selectAdresse);

    // Sélection des champs existants
    const inputRue = document.getElementById("lieu_rue");
    const inputLatitude = document.getElementById("lieu_latitude");
    const inputLongitude = document.getElementById("lieu_longitude");
    const inputVilleNom = document.getElementById("lieu_ville_nom");
    const inputCodePostal = document.getElementById("lieu_code_postal");

    // Initialiser la carte Leaflet
    const map = L.map('map').setView([48.8566, 2.3522], 13); // Paris comme position par défaut

    // Ajouter une couche de tuiles (par exemple, OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    let marker = null; // Marqueur pour la carte, à mettre à jour lors de la sélection d'une adresse

    // Fonction pour rechercher une adresse avec l'API BAN
    inputAutocomplete.addEventListener("input", async () => {
        const query = inputAutocomplete.value.trim();
        if (query.length < 3) {
            selectAdresse.innerHTML = "";
            selectAdresse.style.display = "none";
            return;
        }

        try {
            const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=5`);
            const data = await response.json();

            selectAdresse.innerHTML = "";
            selectAdresse.style.display = "block";

            data.features.forEach((feature) => {
                const resultItem = document.createElement("div");
                resultItem.classList.add("result-item");
                resultItem.textContent = feature.properties.label;
                resultItem.dataset.rue = feature.properties.name || "";
                resultItem.dataset.longitude = feature.geometry.coordinates[0];
                resultItem.dataset.latitude = feature.geometry.coordinates[1];
                resultItem.dataset.ville = feature.properties.city || "";
                resultItem.dataset.code_postal = feature.properties.postcode || "";

                // Ajout de la classe CSS pour le survol et le curseur
                resultItem.style.padding = "8px";
                resultItem.style.cursor = "pointer";
                resultItem.style.borderBottom = "1px solid #ddd";

                // Effet de survol
                resultItem.addEventListener("mouseover", () => {
                    resultItem.style.backgroundColor = "#f0f0f0";
                });
                resultItem.addEventListener("mouseout", () => {
                    resultItem.style.backgroundColor = "white";
                });

                selectAdresse.appendChild(resultItem);

                // Sélectionner l'adresse au clic
                resultItem.addEventListener("click", () => {
                    inputAutocomplete.value = resultItem.textContent;
                    inputRue.value = resultItem.dataset.rue;
                    inputLatitude.value = resultItem.dataset.latitude;
                    inputLongitude.value = resultItem.dataset.longitude;
                    inputVilleNom.value = resultItem.dataset.ville;
                    inputCodePostal.value = resultItem.dataset.code_postal;

                    selectAdresse.style.display = "none"; // Fermer la liste après la sélection

                    // Mettre à jour le marqueur et la position de la carte
                    const latitude = parseFloat(resultItem.dataset.latitude);
                    const longitude = parseFloat(resultItem.dataset.longitude);

                    // Si un marqueur existe déjà, on le met à jour, sinon on en crée un nouveau
                    if (marker) {
                        marker.setLatLng([latitude, longitude]);
                    } else {
                        marker = L.marker([latitude, longitude]).addTo(map);
                    }

                    // Centrer la carte sur la nouvelle position
                    map.setView([latitude, longitude], 13);
                });
            });

        } catch (error) {
            console.error("Erreur lors de la récupération des adresses :", error);
        }
    });
});
