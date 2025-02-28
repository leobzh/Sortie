document.addEventListener("DOMContentLoaded", () => {
    const inputAutocomplete = document.getElementById("lieu_autocomplete");
    const selectAdresse = document.createElement("div");  // Utilisation d'un div au lieu d'un select
    selectAdresse.id = "adresse-results"; // ID unique pour la liste des résultats
    selectAdresse.style.display = "none"; // Initialement caché
    selectAdresse.style.position = "absolute"; // Positionnement absolu pour l'affichage des résultats
    selectAdresse.style.backgroundColor = "white"; // Fond blanc
    selectAdresse.style.border = "1px solid #ccc"; // Bordure légère pour la séparation
    selectAdresse.style.boxShadow = "0 2px 5px rgba(0, 0, 0, 0.1)"; // Ombre pour donner un peu de profondeur
    selectAdresse.style.width = inputAutocomplete.offsetWidth + "px"; // Largeur du même niveau que le champ de saisie
    inputAutocomplete.parentNode.appendChild(selectAdresse); // Ajouter la div dans le DOM

    // Sélection des champs existants
    const inputRue = document.getElementById("lieu_rue");
    const inputLatitude = document.getElementById("lieu_latitude");
    const inputLongitude = document.getElementById("lieu_longitude");
    const inputVilleNom = document.getElementById("lieu_ville_nom");
    const inputCodePostal = document.getElementById("lieu_code_postal");

    // Fonction pour rechercher une adresse avec l'API BAN
    inputAutocomplete.addEventListener("input", async () => {
        const query = inputAutocomplete.value.trim();
        if (query.length < 3) {
            selectAdresse.innerHTML = "";  // Efface les résultats si moins de 3 caractères
            selectAdresse.style.display = "none";  // Cache la liste
            return;
        }

        try {
            const response = await fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=5`);
            const data = await response.json();

            // Effacer les anciens résultats
            selectAdresse.innerHTML = "";
            selectAdresse.style.display = "block";  // Afficher la liste des résultats

            // Ajouter les résultats dans la liste
            data.features.forEach((feature) => {
                const resultItem = document.createElement("div");
                resultItem.classList.add("result-item");
                resultItem.textContent = feature.properties.label;
                resultItem.dataset.rue = feature.properties.name || "";
                resultItem.dataset.longitude = feature.geometry.coordinates[0];
                resultItem.dataset.latitude = feature.geometry.coordinates[1];
                resultItem.dataset.ville = feature.properties.city || "";
                resultItem.dataset.code_postal = feature.properties.postcode || "";

                // Ajouter l'élément dans la liste
                selectAdresse.appendChild(resultItem);

                // Ajouter un écouteur d'événement pour la sélection d'un résultat
                resultItem.addEventListener("click", () => {
                    inputAutocomplete.value = resultItem.textContent;
                    inputRue.value = resultItem.dataset.rue;
                    inputLatitude.value = resultItem.dataset.latitude;
                    inputLongitude.value = resultItem.dataset.longitude;
                    inputVilleNom.value = resultItem.dataset.ville;
                    inputCodePostal.value = resultItem.dataset.code_postal;

                    selectAdresse.style.display = "none";  // Fermer la liste après la sélection
                });

                // Modifier le curseur pour indiquer la possibilité de cliquer
                resultItem.style.cursor = "pointer";  // Curseur en forme de main (pointer)

                // Ajouter un effet de survol pour la sélection visuelle
                resultItem.style.padding = "8px";
                resultItem.style.borderBottom = "1px solid #ddd";  // Ajout d'une bordure légère
                resultItem.style.transition = "background-color 0.3s";  // Transition douce pour le changement de couleur de fond

                // Effet de survol
                resultItem.addEventListener("mouseenter", () => {
                    resultItem.style.backgroundColor = "#f0f0f0";  // Changer la couleur de fond au survol
                });
                resultItem.addEventListener("mouseleave", () => {
                    resultItem.style.backgroundColor = "";  // Réinitialiser la couleur de fond
                });
            });

        } catch (error) {
            console.error("Erreur lors de la récupération des adresses :", error);
        }
    });
});
