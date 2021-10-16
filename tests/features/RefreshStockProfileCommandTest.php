<?php


namespace App\Tests\features;


use App\Entity\Stock;
use App\Tests\DatabasePrimer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class RefreshStockProfileCommandTest extends KernelTestCase
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function setUp(): void
    {
        $kernel = self::bootKernel();
        DatabasePrimer::prime($kernel);
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
    }

    /** @test */
    public function the_refresh_stock_profile_command_behaves_correctly()
    {
        //Setup
        $application = new Application(self::$kernel);

        // Command
        $command = $application->find('app:refresh-stock-profile');

        $commandTester = new CommandTester($command);

        // Do something
        $commandTester->execute(
            [
                'symbol' => 'AMZN',
                'region' => 'US',

            ]
        );

        // Assertions
        $stockRepo = $this->entityManager->getRepository(Stock::class);

        /** @var Stock $stockRecord */
        $stockRecord = $stockRepo->findOneBy(['symbol' => 'AMZN']);

        // Make assertions
        $this->assertSame('USD', $stockRecord->getCurrency());
        $this->assertSame('NasdaqGS', $stockRecord->getExchangeName());
        $this->assertSame('AMZN', $stockRecord->getSymbol());
        $this->assertSame('Amazon.com, Inc.', $stockRecord->getShortName());
        $this->assertSame('US', $stockRecord->getRegion());
        $this->assertGreaterThan(50, $stockRecord->getPreviousClose());
        $this->assertGreaterThan(50, $stockRecord->getPrice());

    }


}