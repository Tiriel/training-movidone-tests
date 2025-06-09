<?php

namespace App\Controller\Api;

use App\Entity\Volunteer;
use App\Matching\VolunteerMatcher;
use App\Repository\VolunteerRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class VolunteerController extends AbstractController
{
    public function __construct(
        private readonly VolunteerMatcher $volunteerMatcher,
    ) {
    }

    #[IsGranted('IS_AUTHENTICATED')]
    #[Route('/api/volunteers', name: 'app_api_volunteers')]
    public function getVolunteersApi(Request $request, VolunteerRepository $repository): Response
    {
        $limit = 20;
        $page = $request->query->getInt('page', 1);
        $volunteers = $repository->findBy([], [], $limit, ($page - 1) * $limit);

        return $this->json($volunteers, Response::HTTP_OK, [], ['groups' => ['Volunteer']]);
    }

    #[Route('/{id}/matches', name: 'api_volunteer_matches', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    #[OA\Response(
        response: 200,
        description: 'Returns matching volunteers based on the specified strategy',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(ref: new Model(type: Volunteer::class))
        )
    )]
    #[OA\Parameter(
        name: 'strategy',
        in: 'query',
        description: 'The matching strategy to use',
        schema: new OA\Schema(type: 'string')
    )]
    public function getMatches(Request $request, Volunteer $volunteer): JsonResponse
    {
        $strategy = $request->query->get('strategy');

        if ($strategy) {
            $matches = $this->volunteerMatcher->findMatches($volunteer, $strategy);
        } else {
            $matches = $this->volunteerMatcher->findMatchesUsingAllStrategies($volunteer);
        }

        return $this->json([
            'matches' => $matches,
            'available_strategies' => array_map(
                fn ($strategy) => [
                    'name' => $strategy->getName(),
                    'description' => $strategy->getDescription(),
                ],
                $this->volunteerMatcher->getStrategies()
            ),
        ]);
    }
}
