<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;


class ClosedListener
{
    private $closedFile;
    private $twig;


    /**
     * ClosedListener constructor.
     */
    public function __construct(Environment $twig, $closedFilePath)
    {
        $this->closedFile = $closedFilePath;
        $this->twig = $twig;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $closed = file_exists($this->closedFile);

        // Rien à faire si le site n'est pas fermé
        if (!$closed) return;

        // Rien à faire si on est en admin
        if (strpos($event->getRequest()->getPathInfo(), 'admin') !== false) return;

        $template = $this->twig->render('game/closed.html.twig');
        $event->setResponse(new Response($template));
        $event->stopPropagation();
    }
}
