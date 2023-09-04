<?php

namespace App\Controller;

use App\Entity\Player;
use App\Service\SMSService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;

class GpdcController extends AbstractController
{
    #[Route(path: '/sms_random_player', name: 'gpdc_sms_random_player')]
    public function sendNotif(
        Request $request,
        SMSService $sms,
        EntityManagerInterface $em,
        #[Autowire(env: 'GPDC_TEL_ANIMATEUR')]
        string $gpdcTelAnimateur
    ) {

        // Check tel
        $tel = $request->request->get("senderid");
        $message = $request->request->get("message");
        $gpdcTelAnimateur = preg_replace('#[^0-9+]#', '', $gpdcTelAnimateur);
        $gpdcTelAnimateur = preg_replace('#^0#', '+33', $gpdcTelAnimateur);
        if ($tel != $gpdcTelAnimateur && $message != "tirage") {
            throw new AccessDeniedHttpException();
        }

        $players = $em->getRepository(Player::class)->findforRandom();

        if (count($players) == 0) {
            $sms->send($gpdcTelAnimateur, "Aucun joueur disponible");
            return new Response("OK");
        }

        /** @var Player $player */
        $player = $players[random_int(0, count($players) - 1)];
        $player->setLastRandom(new \DateTime());
        $em->flush();

        $sms->send($gpdcTelAnimateur, $player->getNomComplet());
        return new Response("OK");
    }

}
