@echo off
echo Exécution de la vérification des événements...
cd %~dp0
php bin/console app:check-event-status
echo Vérification terminée avec succès !
pause