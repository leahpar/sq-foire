<?php

namespace App\Controller;

use App\Controller\Admin\PlayerCrudController;
use App\Controller\Admin\SmsCrudController;
use App\Entity\Answer;
use App\Entity\Player;
use App\Entity\Sms;
use App\Service\SMSService;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AdminController extends AbstractController
{
    public function __construct(
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {}

    private function redirectToAdminRoute(string $controller, string $action)
    {
        return $this->redirect(
            $this->adminUrlGenerator->setController($controller)->setAction($action)->generateUrl()
        );
    }

    #[Route(path: '/admin/rand_player', name: 'rand_player')]
    public function randomPlayer(EntityManagerInterface $em, UrlGeneratorInterface $router)
    {
        $players = $em->getRepository(Player::class)->findforRandom();

        if (count($players) > 0) {
            /** @var Player $player */
            $player = $players[random_int(0, count($players) - 1)];
            $player->setLastRandom(new \DateTime());
            $em->flush();

            $msg = "$player";
            $msg .= " - ";
            $msg .= "<a href='".$router->generate('notif_player', ["id" => $player->getId()])."'>Envoyer SMS</a>";
            $this->addFlash("success", $msg);
        }
        else {
            $this->addFlash("warning", "Aucun joueur disponible");
        }

        return $this->redirectToAdminRoute(PlayerCrudController::class, 'index');
    }

    #[Route(path: '/admin/reload', name: 'reload_players')]
    public function reloadPlayers(Request $request, EntityManagerInterface $em)
    {
        $time = $request->query->get("t", 0);
        $date = (new \DateTime())->setTimestamp($time);
        $count = $em->getRepository(Player::class)->checkForReload($date);

        return new Response(null, $count > 0 ? 200 : 304);
    }

    #[Route(path: '/admin/notif/{id}', name: 'notif_player')]
    public function sendNotif(
        Player $player,
        SMSService $sms,
        #[Autowire(env: 'GPDC_TEL_ANIMATEUR')]
        ?string $gpdcTelAnimateur = null
    ) {
        $target = $player->getTelephone();

        if (!$player->isRandom()) {
            $this->addFlash("warning", "Joueur $player non tiré au sort");
        }
        elseif ($gpdcTelAnimateur)  {
            $sms->send($gpdcTelAnimateur, "tirage au sort : {$player->getNomComplet()}");
            $this->addFlash("success", "SMS envoyé (à l'animateur)");
        }
        elseif ($target)  {
            $message = "Bravo ! Vous avez été tiré au sort, venez récupérer votre lot au stand animation GSA 
                à l'entrée de la foire. Un soucis ? 0607840001";
            $sms->send($target, $message);
            $this->addFlash("success", "SMS envoyé (au joueur)");
        }
        else {
            $this->addFlash("warning", "Joueur $player sans numéro de téléphone");
        }

        return $this->redirectToAdminRoute(PlayerCrudController::class, 'index');
    }

    #[Route(path: '/admin/stats', name: 'admin_stats')]
    public function stats(EntityManagerInterface $em)
    {
        $players = $em->getRepository(Player::class)->findAll();

        $share = 0;
        $emil = 0;
        $sms = 0;
        $smsetemail = 0;
        $smsouemail = 0;
        $actifHeure = 0;
        $actifJour = 0;
        $tiragesTotal = 0;
        $tiragesJour = 0;

        //date_default_timezone_set("Europe/Paris");
        $now = (new \DateTime)->modify("-1 hour");
        $today = (new \DateTime)->setTime(0, 0);

        /** @var Player $player */
        foreach ($players as $player) {
            if ($player->getFbshare()) $share++;
            if ($player->getData()["authMailPub"]??"off" == "on") $emil++;
            if ($player->getData()["authSmsPub"]??"off" == "on") $sms++;
            if (($player->getData()["authSmsPub"]??"off" == "on")
             && ($player->getData()["authMailPub"]??"off" == "on")) $smsetemail++;
            if (($player->getData()["authSmsPub"]??"off" == "on")
             || ($player->getData()["authMailPub"]??"off" == "on")) $smsouemail++;
            if ($player->getLastConnection() >= $now) $actifHeure++;
            if ($player->getLastConnection() >= $today) $actifJour++;
            if ($player->getLastRandom()) $tiragesTotal++;
            if ($player->getLastRandom() && $player->getLastRandom() >= $today) $tiragesJour++;
        }

        $notes = $em->getRepository(Answer::class)->getNotes();

        return $this->render('admin/stats.html.twig', [
            "share" => $share,
            "sms" => $sms,
            "email" => $emil,
            "smsetemail" => $smsetemail,
            "smsouemail" => $smsouemail,
            "total" => count($players)?:1,
            "actifHeure" => $actifHeure,
            "actifJour" => $actifJour,
            "tiragesTotal" => $tiragesTotal,
            "tiragesJour" => $tiragesJour,
            "notes" => $notes,
        ]);
    }

    #[Route(path: '/admin/export')]
    public function export(EntityManagerInterface $em)
    {
        $players = $em->getRepository(Player::class)->findAll();

        $output = implode(';', [
            "ID",
            "Nom",
            "Email",
            "Tel",
            "Ville",
            "Réponses",
            "Bonnes réponses",
            "Tirage au sort",
            "Partage Facebook"
        ]);
        $output .= "\n";

        foreach ($players as $player) {

            // RGPD : les joueurs n'ayant rien partagés ne sont pas à afficher
            if (!$player->authMailPub && !$player->authSmsPub) {
                continue;
            }

            $output .= implode(';', [
                $player->getId(),
                $player->getName(),
                $player->authMailPub ? $player->getEmail() : '',
                $player->authSmsPub  ? $player->tel        : '',
                $player->ville,
                $player->countAnswers(),
                $player->countGoodAnswers(),
                $player->getLastRandom() != null ? "oui" : "non",
                $player->getFbshare()    != null ? "oui" : "non",

            ]);
            $output .= "\n";
        }

        return new Response($output);
    }

    #[Route(path: '/admin/testsms')]
    public function testsms(Request $request, SMSService $sms)
    {
        $message = $request->query->get("txt") ?? "test";
        $target  = $request->query->get("tel") ?? null;

        if ($target)  {
            $sms->send($target, $message);
        }

        return new Response();
    }

    #[Route(path: '/admin/podium', name: 'admin_podium')]
    public function podium(EntityManagerInterface $em)
    {
        $notes = $em->getRepository(Answer::class)->getNotes();

        return $this->render('admin/podium.html.twig', [
            "notes" => $notes,
        ]);
    }

    #[Route(path: '/admin/roll', name: 'admin_roll')]
    public function roll(EntityManagerInterface $em)
    {
        $notes = $em->getRepository(Answer::class)->getNotes();

        return $this->render('admin/roll.html.twig', [
            "notes" => $notes,
        ]);
    }

    #[Route(path: '/admin/onoff', name: 'admin_onoff')]
    public function onOff(Request $request, string $closedFilePath)
    {
        $params = $request->query->all();
        $closed = $params['routeParams']['closed'] ?? false;
        try {
            if ($closed) {
                touch($closedFilePath);
                $this->addFlash("warning", "Site fermé");
            } else {
                @unlink($closedFilePath);
                $this->addFlash("success", "Site ouvert");
            }
        }
        catch (\Exception $e) {
            $this->addFlash("error", $e->getMessage());
        }

        return $this->redirectToAdminRoute(PlayerCrudController::class, 'index');
    }

    #[Route(path: '/admin/envoyersms', name: 'envoyer')]
    public function envoyerSms(Request $request, SMSService $SMSService, EntityManagerInterface $em)
    {
        // controllers extending the EasyAdminController get access to the
        // following variables:
        //   $this->request, stores the current request
        //   $this->em, stores the Entity Manager for this Doctrine entity

        // change the properties of the given entity and save the changes
        $id = $request->query->get('id');
        $sms = $em->getRepository(Sms::class)->find($id);

        $players = $em->getRepository(Player::class)->findAll();
        $today = (new \DateTime)->setTime(0, 0);

        $cpt = 0;
        /** @var Player $player */
        foreach ($players as $player) {
            if (($player->getData()["authSmsPub"]??"off") != "on") continue;
            if ($player->getLastConnection() < $today) continue;

            $SMSService->send($player->getTelephone(), $sms->getMessage());
            $cpt++;
        }

        $this->addFlash("success", $cpt." messages envoyés");

        return $this->redirectToAdminRoute(SmsCrudController::class, 'index');

    }

}
