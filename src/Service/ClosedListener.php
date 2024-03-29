<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Twig\Environment;


class ClosedListener
{
    public function __construct(
        private readonly Environment $twig,
        private readonly string $closedFilePath
    ) {}

    public function onKernelRequest(RequestEvent $event)
    {
        $closed = file_exists($this->closedFilePath);

        // Rien à faire si le site n'est pas fermé
        if (!$closed) return;

        // Rien à faire si on est en admin
        if (str_contains($event->getRequest()->getPathInfo(), 'admin')) return;

        $template = $this->twig->render('game/closed.html.twig');
        $event->setResponse(new Response($template));
        $event->stopPropagation();
    }
}
