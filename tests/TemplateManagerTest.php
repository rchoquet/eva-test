<?php

class TemplateManagerTest extends PHPUnit_Framework_TestCase
{
    private $appContext;
    private $quoteRepo;
    private $siteRepo;
    private $destinationRepo;
    private $templateManager;

    /**
     * Init the mocks
     */
    public function setUp()
    {
        $this->appContext = new \Deadbeef\Context\ApplicationContext();
        $this->quoteRepo = new \Deadbeef\Repository\QuoteRepository();
        $this->siteRepo = new \Deadbeef\Repository\SiteRepository();
        $this->destinationRepo = new \Deadbeef\Repository\DestinationRepository();

        $this->templateManager = new \Deadbeef\TemplateManager(
            $this->appContext,
            $this->quoteRepo,
            $this->siteRepo,
            $this->destinationRepo
        );
    }

    /**
     * Closes the mocks
     */
    public function tearDown()
    {
    }

    /**
     * @test
     */
    public function test()
    {
        $faker = \Faker\Factory::create();

        $expectedDestination = $this->destinationRepo->getById($faker->randomNumber());
        $expectedUser = $this->appContext->getCurrentUser();

        $quote = new \Deadbeef\Entity\Quote($faker->randomNumber(), $faker->randomNumber(), $faker->randomNumber(), $faker->date());

        $template = new \Deadbeef\Entity\Template(
            1,
            'Votre voyage avec une agence locale [quote:destination_name]',
            "
Bonjour [user:first_name],

Merci d'avoir contacté un agent local pour votre voyage [quote:destination_name].

Bien cordialement,

L'équipe Evaneos.com
www.evaneos.com
");

        $message = $this->templateManager->getTemplateComputed(
            $template,
            [
                'quote' => $quote
            ]
        );

        $this->assertEquals('Votre voyage avec une agence locale ' . $expectedDestination->countryName, $message->subject);
        $this->assertEquals("
Bonjour " . $expectedUser->firstname . ",

Merci d'avoir contacté un agent local pour votre voyage " . $expectedDestination->countryName . ".

Bien cordialement,

L'équipe Evaneos.com
www.evaneos.com
", $message->content);
    }
}
