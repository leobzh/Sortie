document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.toggle-user').forEach(function (toggle) {

        toggle.addEventListener('change', function () {

            let utilisateurId = this.dataset.id;
            let isChecked = this.checked;


            console.log("Tentative de mise à jour pour ID");

            fetch(`/admin/utilisateur/toggle/${utilisateurId}`,{
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert("Erreur de mise à jour");
                        this.checked = !isChecked;
                    } else {
                        this.nextElementSibling.textContent = data.isActif ? "Activé" : "Désactivé";
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    this.checked = !isChecked;
                })
        })
    })
})