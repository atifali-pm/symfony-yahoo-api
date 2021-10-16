<?php

namespace App\Command;

use App\Entity\Stock;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RefreshStockProfileCommand extends Command
{
    protected static $defaultName = 'app:refresh-stock-profile';
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Retrieve a stock profile from the Yahoo Finance API. Update the record in the DB')
            ->addArgument('symbol', InputArgument::REQUIRED, 'Stock symbol e.g. AMZN for Amazon')
            ->addArgument('region', InputArgument::REQUIRED, 'The region of the company e.g. US for United States')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stock = new Stock();

        $stock->setSymbol('AMZN');
        $stock->setShortName('Amazon.com, Inc.');
        $stock->setCurrency('USD');
        $stock->setExchangeName('NasdaqGS');
        $stock->setRegion('US');
        $price = 1000;
        $previousClose = 1100;
        $priceChange = $price - $previousClose;
        $stock->setPrice($price);
        $stock->setPreviousClose($previousClose);
        $stock->setPriceChange($priceChange);

        $this->entityManager->persist($stock);
        $this->entityManager->flush();

        return Command::SUCCESS;
    }
}
