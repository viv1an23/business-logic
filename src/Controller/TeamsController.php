<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Team;
use App\Form\SellPlayersType;
use App\Form\TeamType;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Service\TransactionHelper;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class TeamsController extends AbstractController
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly TeamRepository $teamRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly TransactionHelper $transactionHelper
    ) {
    }
    #[Route('/teams', name: 'app_teams')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $numberOfRecords = $request->query->getInt('numberOfRecords', 25);
        $queryBuilder = $this->teamRepository->findAll();

        $teams = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), $numberOfRecords, [
            'defaultSortFieldName' => 'id',
            'defaultSortDirection' => 'desc',
        ]);
        return $this->render('teams/index.html.twig', [
            'teams' => $teams,
            'numberOfRecords' => $numberOfRecords,
        ]);
    }

    #[Route('/teams/action/{id?}', name: 'app_team_action')]
    public function create(Request $request, Team $team = null): Response
    {
        $team = $team instanceof Team ? $team : new Team();

        $form = $this->createForm(TeamType::class, $team);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->teamRepository->save($team, true);
            return $this->redirectToRoute('app_teams');
        }
        return $this->render('teams/action.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/teams/{id?}/players', name: 'team_players')]
    public function teamPlayer(Team $team): Response
    {

        $players = $this->playerRepository->findBy(
            ['team' => $team],
            ['id' => 'desc']
        );
        return $this->render('teams/show.html.twig', [
            'team' => $team,
            'tab' => 'sell_player',
            'players' => $players,
        ]);
    }

    #[Route('/teams/buy/{id}/{pId?}', name: 'app_buy_player')]
    #[ParamConverter('player', options: ['id' => 'pId'])]
    public function buyPlayer(Request $request, Team $team, Player $player = null): Response
    {
        if ($player != null) {
            if (!$this->transactionHelper->buyPlayer($player, $team)) {
                $this->addFlash('danger', "can't Buy Player, Money Balance Is Less");
            }
            return $this->redirectToRoute('team_players', ['id' => $team->getId()]);
        }

        $players = $this->playerRepository->createQueryBuilder('pr')
                    ->where('pr.team IS NULL')
                    ->orWhere('pr.IsAvailableForSell != :IsAvailableForSell')
                    ->setParameter('IsAvailableForSell', false)->getQuery()->getArrayResult();

        return $this->render('teams/player/buy_player.html.twig', [
            'teamId' => $team->getId(),
            'players' => $players
        ]);
    }

    #[Route('/teams/sell/{id}', name: 'app_sell_player')]
    public function sellPlayer(Request $request, Player $player): Response
    {
        $player->setSellingPrice($player->getPrice());
        $form = $this->createForm(SellPlayersType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            try {
                $player->setIsAvailableForSell(true);
                $this->playerRepository->save($player, true);
            } catch (Throwable $throwable) {
                dd($throwable->getMessage());
            }
            return $this->redirectToRoute('team_players', [
                'id' => $player->getTeam()?->getId()
            ]);
        }

        return $this->render('teams/player/sell_player.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/teams/delete/{id}', name: 'app_team_delete')]
    public function delete(Team $team): Response
    {
        $players = $this->playerRepository->findBy(['team' => $team]);
        foreach ($players as $player) {
            $player->setTeam(null);
            $this->entityManager->persist($player);
        }
        $this->entityManager->flush();
        $this->teamRepository->remove($team, true);
        return $this->redirectToRoute('app_teams');
    }
}
