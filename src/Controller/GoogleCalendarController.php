<?php

namespace App\Controller;

use Google\Client;
use Google\Service\Calendar;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoogleCalendarController extends AbstractController
{
    #[Route('/sync-google-calendar', name: 'sync_google_calendar', methods: ['POST'])]
    public function sync(Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);

            if (!$data || !isset($data['name'], $data['location'], $data['start'], $data['duration'])) {
                throw new \Exception('Données de requête invalides.');
            }

            // Récupérer l'utilisateur connecté
            $user = $this->getUser();
            if (!$user) {
                throw new \Exception('Utilisateur non authentifié.');
            }

            // Récupérer la clé API de l'utilisateur
            $apiKey = $user->getApiKey();
            if (!$apiKey) {
                throw new \Exception('Clé API non trouvée.');
            }

            // Configurer le client Google avec la clé API
            $client = new Client();
            $client->setApplicationName("Calendar");
            $client->setDeveloperKey($apiKey);

            $calendarService = new Calendar($client);
            $event = new \Google_Service_Calendar_Event([
                'summary' => $data['name'],
                'location' => $data['location'],
                'start' => ['dateTime' => $data['start'], 'timeZone' => 'Europe/Paris'],
                'end' => ['dateTime' => date('Y-m-d\TH:i:s', strtotime($data['start'] . " + " . $data['duration'] . " minutes")), 'timeZone' => 'Europe/Paris'],
            ]);

            $calendarService->events->insert('primary', $event);

            return new JsonResponse(['success' => true]);
        } catch (\Exception $e) {
            // Log the error message for debugging purposes
            error_log($e->getMessage());
            return new JsonResponse(['success' => false, 'message' => 'Une erreur est survenue lors de la synchronisation avec Google Calendar.'], 500);
        }
    }
}
