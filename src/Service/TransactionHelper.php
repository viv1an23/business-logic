<?php

namespace App\Service;

use App\Entity\Player;
use App\Entity\Transaction;
use App\Repository\PlayerRepository;
use App\Repository\TeamRepository;
use App\Repository\TransactionRepository;
use Doctrine\ORM\EntityManagerInterface;

class TransactionHelper
{
    public function __construct(
        private readonly PlayerRepository $playerRepository,
        private readonly TeamRepository $teamRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly TransactionRepository $transactionRepository
    ) {
    }

    public function buyPlayer($player, $team): bool
    {
        /** @var Player $player */
        $price = $player->isIsAvailableForSell() &&
        $player->getSellingPrice() != null ? $player->getSellingPrice() : $player->getPrice();
        if ($price <= $team->getMoneyBalance()) {
            if ($player->isIsAvailableForSell()) {
                $player->getTeam()?->setMoneyBalance($player->getTeam()?->getMoneyBalance() +
                    $player->getSellingPrice());
            }
            $this->transactionLog($team, $player, $price);
            $player->setTeam($team);
            if ($player->isIsAvailableForSell()) {
                $team->setMoneyBalance($team->getMoneyBalance() - $player->getSellingPrice());
                $player->setIsAvailableForSell(false);
                $player->setSellingPrice(null);
            } else {
                $team->setMoneyBalance($team->getMoneyBalance() - $player->getPrice());
            }
            $this->entityManager->persist($player);
            $this->entityManager->persist($team);
        } else {
            return false;
        }
        $this->entityManager->flush();
        return true;
    }

    public function transactionLog($team, $player, $price): void
    {
        $transaction = new Transaction();
        $transaction->setBuyer($team);
        $transaction->setSeller($player->getTeam() ?: null);
        $transaction->setPlayer($player);
        $transaction->setPrice($price);
        $this->transactionRepository->save($transaction, true);
    }
}
