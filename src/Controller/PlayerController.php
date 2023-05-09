<?php

namespace App\Controller;

use App\Entity\Player;
use App\Form\PlayerType;
use App\Repository\PlayerRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{

    public function __construct(private readonly PlayerRepository $playerRepository){

    }

    #[Route('/player', name: 'app_player')]
    public function index(Request $request,  PaginatorInterface $paginator): Response
    {
        $numberOfRecords = $request->query->getInt('numberOfRecords', 25);
        $queryBuilder = $this->playerRepository->findAll();

        $players = $paginator->paginate($queryBuilder, $request->query->getInt('page', 1), $numberOfRecords, [
            'defaultSortFieldName' => 'id',
            'defaultSortDirection' => 'desc',
        ]);
        return $this->render('players/index.html.twig', [
            'players' => $players,
            'numberOfRecords' => $numberOfRecords,
        ]);

    }

    #[Route('/player/action/{id?}', name: 'app_player_action')]
    public function create(Request $request, Player $player = null): Response
    {
        $player = $player instanceof Player ? $player : new Player();

        $form = $this->createForm(PlayerType::class, $player);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->playerRepository->save($player, true);
            return $this->redirectToRoute('app_player');
        }
        return $this->render('players/action.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/player/delete/{id}', name: 'app_player_delete')]
    public function delete(Player $player): Response
    {
        if ($player->getTeam() == null){
            $this->playerRepository->remove($player, true);
        }else{
            $this->addFlash('danger', "Player can't be delete because its in team");
        }
        return $this->redirectToRoute('app_player');
    }
}
