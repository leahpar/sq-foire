<?php

namespace App\Controller;


use App\Entity\Answer;
use App\Entity\Hall;
use App\Entity\Player;
use App\Entity\Question;
use App\Service\SMSService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends AbstractController
{

    #[Route(path: '/', name: 'game_index')]
    public function index()
    {
        if ($this->isGranted("ROLE_USER")) {
            return $this->redirectToRoute("game_halls");
        }
        else {
            return $this->render('game/start.html.twig');
        }
    }

    #[Route(path: '/closed', name: 'game_closed')]
    public function closed()
    {
        return $this->render('game/closed.html.twig');
    }

    #[Route(path: '/comment-jouer', name: 'game_howto')]
    public function howto()
    {
        return $this->render('game/howto.html.twig');
    }

    #[Route(path: '/reglement', name: 'game_rules')]
    public function rules()
    {
        return $this->render('game/rules.html.twig');
    }

    #[Route(path: '/share', name: 'game_share')]
    public function share(Request $request, EntityManagerInterface $em)
    {
        /** @var Player $user */
        $user = $this->getUser();
        if ($user) {
            $user->setFbshare(true);
            $em->flush();
        }
        $url = $request->getHost();
        return $this->redirect("https://www.facebook.com/sharer/sharer.php?u=$url");
    }

}
