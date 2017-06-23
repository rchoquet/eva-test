<?php

require_once __DIR__.'/../vendor/autoload.php';

$faker = \Faker\Factory::create();

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
$templateManager = new \Deadbeef\TemplateManager(
    new \Deadbeef\Context\ApplicationContext(),
    new \Deadbeef\Repository\QuoteRepository(),
    new \Deadbeef\Repository\SiteRepository(),
    new \Deadbeef\Repository\DestinationRepository()
);

$message = $templateManager->getTemplateComputed(
    $template,
    [
        'quote' => new \Deadbeef\Entity\Quote(
            $faker->randomNumber(),
            $faker->randomNumber(),
            $faker->randomNumber(),
            $faker->date()
        )
    ]
);

echo $message->subject."\n".$message->content;
