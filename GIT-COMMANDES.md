# 📄 GIT-COMMANDES.md

Un guide pratique pour utiliser Git dans le projet.

---

## 🔍 1. Vérifier l’état du projet Git  

### ➞ Savoir si on est dans un dépôt Git  
```sh
git rev-parse --show-toplevel
```

### ➞ Vérifier les fichiers modifiés/non commités  
```sh
git status
```

### ➞ Voir l’historique des commits  
```sh
git log --oneline
```

---

## 🏁 2. Initialisation d’un projet Git  

### ➞ Initialiser un dépôt Git (uniquement au début)  
```sh
git init
```

### ➞ Ajouter un dépôt distant  
```sh
git remote add origin https://github.com/utilisateur/nom-du-repo.git
```

---

## 📄 3. Ajouter et envoyer des changements  

### ➞ Ajouter tous les fichiers modifiés  
```sh
git add .
```

### ➞ Créer un commit avec un message  
```sh
git commit -m "Message décrivant les changements"
```

### ➞ Envoyer les changements vers la branche principale  
```sh
git push origin main
```

### ➞ Forcer l’envoi (⚠️ Attention à ne pas écraser le travail des autres)  
```sh
git push --force origin main
```

---

## 📅 4. Récupérer les mises à jour  

### ➞ Télécharger et fusionner les modifications  
```sh
git pull origin main
```

### ➞ Récupérer sans fusionner immédiatement (pull + merge manuel)  
```sh
git fetch origin
```

### ➞ Résoudre un conflit de push (problème de fast-forward)  
```sh
git pull --rebase origin main
git push origin main
```

---

## 🔀 5. Gestion des branches  

### ➞ Lister toutes les branches  
```sh
git branch -a
```

### ➞ Créer une nouvelle branche  
```sh
git checkout -b nom-de-branche
```

### ➞ Changer de branche  
```sh
git checkout nom-de-branche
```

### ➞ Fusionner une branche avec `main`  
```sh
git merge nom-de-branche
```

### ➞ Supprimer une branche  
```sh
git branch -d nom-de-branche
```

---

## 🔄 6. Annuler des modifications  

### ➞ Annuler un fichier modifié avant `git add`  
```sh
git checkout -- fichier.txt
```

### ➞ Annuler un `git add`  
```sh
git reset fichier.txt
```

### ➞ Annuler le dernier commit (sans perdre les fichiers)  
```sh
git reset --soft HEAD~1
```

### ➞ Annuler tous les changements et revenir au dernier commit  
```sh
git reset --hard
```

---

## 👥 7. Travailler en équipe (GitHub)  

### ➞ Ajouter un développeur au repo GitHub  
1. Aller sur **GitHub** → ⚙️ **Settings** du repo.  
2. **Manage Access** → Ajouter un collaborateur avec son pseudo GitHub.  
3. Il devra accepter l’invitation pour contribuer.  

---

Avec ce guide, tout est clair pour utiliser Git efficacement ! 🚀

