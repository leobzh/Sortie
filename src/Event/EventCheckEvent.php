<?php
// src/Event/EventCheckEvent.php
namespace App\Event;
use Symfony\Contracts\EventDispatcher\Event;
class EventCheckEvent extends Event
{
    public const NAME = 'event.check';
// Aucune donnée supplémentaire n'est nécessaire pour cet événement
}