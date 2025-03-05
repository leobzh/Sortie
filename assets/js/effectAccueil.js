document.addEventListener("DOMContentLoaded", function() {
    function typeWriterEffect(elementId, text, delay = 20) {
        const element = document.getElementById(elementId);
        let index = 0;

        function type() {
            if (index < text.length) {
                element.innerHTML += text.charAt(index);
                index++;
                setTimeout(type, delay);
            }
        }

        type();
    }

    // Texte à animer
    const texts = {
        "typing-text-1": "Sortie App vous permet de créer, gérer et participer à des événements et des sorties. Que ce soit pour organiser une randonnée, une soirée cinéma ou un événement professionnel, notre application vous simplifie la vie.",
        "typing-text-2": "Créez et gérez facilement vos sorties et événements en quelques clics.",
        "typing-text-3": "Invitez vos amis et suivez les participants à vos événements.",
        "typing-text-4": "Visualisez tous vos événements à venir dans un calendrier intuitif."
    };

    // Appliquer l'effet à chaque texte
    for (const [elementId, text] of Object.entries(texts)) {
        typeWriterEffect(elementId, text);
    }
});
