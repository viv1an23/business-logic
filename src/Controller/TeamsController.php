<?php

namespace App\Controller;

use App\Entity\Player;
use App\Entity\Team;
use App\Form\AddPlayersType;
use App\Form\TeamType;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
        private readonly EntityManagerInterface $entityManager){

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

    #[Route('/teams/sell/{id?}', name: 'app_sell_player')]
    public function sellPlayer(Team $team): Response
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

    #[Route('/teams/buy/{id?}', name: 'app_buy_player')]
    public function buyPlayer(Request $request, Team $team): Response
    {
        $form = $this->createForm(AddPlayersType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            try {

                $players = $form->get('players')->getData();
                /** @var Player $player*/
                foreach ($players as $player) {
                    if ($player->getPrice() <= $team->getMoneyBalance()){
                        $player->setTeam($team);
                        $team->setMoneyBalance($team->getMoneyBalance() - $player->getPrice());
                        $this->entityManager->persist($player);
                        $this->entityManager->persist($team);
                    } else{
                        $this->addFlash('danger', "Can't Buy Player, Money Balance Is Less");
                    }
                }
                $this->entityManager->flush();
            } catch (Throwable $throwable) {
                $this->addFlash('danger', "can't Buy Player, Money Balance Is Less");
            }
            return $this->redirectToRoute('app_sell_player', ['id' => $team->getId()]);
        }


        return $this->render('teams/player/buy_player.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
