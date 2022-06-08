<?php

namespace Gerhardn\NatisBot\Command;

use Gerhardn\NatisBot\Helper\ParameterLoader;
use Gerhardn\NatisBot\Helper\TelegramHelper;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\SessionCookieJar;
use GuzzleHttp\RequestOptions;
use mysql_xdevapi\XSession;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PollLicenseStatusCommand extends Command
{

    public static $enatisStatusArray = [
        "01" => "INITIATED",
        "02" => "ORDERED",
        "03" => "Production Pending",
        "04" => "In Production",
        "05" => "Produced - Pending Dispatch",
        "06" => "On Route to Testing Station",
        "07" => "MISPRINTED",
        "08" => "MISPRINTED - SUPERSEDED",
        "09" => "Ready For Collection",
        "10" => "Collected",
        "11" => "NOT COLLECTED BY DRIVER",
        "12" => "INVALID - LOST/DESTROYED/STOLEN",
        "13" => "INVALID - CANCELLED",
        "14" => "INVALID - DEFACED",
        "15" => "NEVER PRODUCED",
        "16" => "NEVER PRODUCED - SUPERSEDED",
        "17" => "RETAINED",
        "18" => "NEVER ORDERED",
        "19" => "NEVER ORDERED - SUPERSEDED"
    ];

    protected function configure()
    {
        $this->setName('poll:natis:license_status')
            ->setDescription('A command to check natis online system for the status of your license');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parameterLoader = new ParameterLoader();

        foreach ($parameterLoader->get('natis_details') as $key => $detail) {
            $jar = new SessionCookieJar('PHPSESSID', true);
            $client = new Client(['cookies' => $jar]);

            $response = $client->post(
                'https://oauth.natis.gov.za/rtmc-jwt-auth/auth/login',
                [RequestOptions::JSON => ['username' => $detail['natis_username'], 'password' => $detail['natis_password']]]
            );

            $token = $response->getHeader('token');
            $responseArray = json_decode($response->getBody()->getContents(), true);
            $licenseNumber = $responseArray['attributes']['ownerPer'];

            $licenseReponse = $client->get(
                'https://online.natis.gov.za/vehicle-renewal-service/bookings/combined/',
                [
                    RequestOptions::HEADERS => [
                        'token' => $token,
                        'peridn' => $licenseNumber
                    ]
                ]
            );

            $licenseCardQueryResponseArray = json_decode($licenseReponse->getBody()->getContents(), true);

            if (!isset($licenseCardQueryResponseArray['liccardInfo'])) {
                throw new \Exception("Looks like NATIS has no record of you ordering a card");
            }

            $licenseCardStat = $licenseCardQueryResponseArray['liccardInfo']['cardstat'];

            $message = "{$key} your drivers license card status is *" . self::$enatisStatusArray[$licenseCardStat]."*";
            $output->writeln($message);

            $telegramHelper = new TelegramHelper();
            $telegramHelper->sendMessage($message);
        }



        return 0;
    }

}