<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Matching\Strategy\TagBasedStrategy;
use App\Search\DatabaseEventSearch;
use App\Search\EventSearchInterface;
use App\Security\Voter\EditionVoter;
use Symfony\Bridge\Twig\Attribute\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use App\Matching\VolunteerMatcher;
use App\Entity\Volunteer;

class EventController extends AbstractController
{
    #[Route('/events', name: 'app_event_list', methods: ['GET'])]
    public function listEvents(Request $request, DatabaseEventSearch $eventSearch): Response
    {
        $events = $eventSearch->searchByName($request->query->get('name', null));

        return $this->render('event/list_events.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/events/search', name: 'app_event_search', methods: ['GET'])]
    #[Template('event/list_events.html.twig')]
    public function searchEvents(Request $request, EventSearchInterface $search): array
    {
        $events = $search->searchByName($request->query->get('name', null))['hydra:member'];

        return ['events' => $events];
    }

    #[Route('/event/{id}', name: 'app_event_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function showEvent(Event $event): Response
    {
        return $this->render('event/show_event.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/event/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    #[Route('/event/{id}/edit', name: 'app_event_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function newEvent(?Event $event): Response
    {
        if ($event instanceof Event) {
            $this->denyAccessUnlessGranted(EditionVoter::EVENT, $event);
        }

        return $this->render('event/new_event.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/events/match/{strategy}', name: 'app_event_match', requirements: ['strategy' => 'tag|skill|location'])]
    public function match(string $strategy, #[CurrentUser] User $user, VolunteerMatcher $matcher): Response
    {
        $volunteer = new Volunteer();
        $volunteer->setForUser($user);

        $events = $matcher->findMatches($volunteer, $strategy.'_based');

        return $this->render('event/list_events.html.twig', [
            'events' => $events,
        ]);
    }
}
