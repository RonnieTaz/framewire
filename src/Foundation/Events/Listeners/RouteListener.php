<?php

namespace Framewire\Foundation\Events\Listeners;

use Aura\Router\Matcher;
use Aura\Router\RouterContainer;
use Aura\Router\Rule\Accepts;
use Aura\Router\Rule\Allows;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RouteListener implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @param RequestEvent $event
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function handle(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->attributes->has('_controller')) {
            // routing is already done
            return;
        }

        //TODO: Log Request Info

        /**
         * @var $matcher Matcher
         */
        $matcher = $this->getContainer()->get(RouterContainer::class)->getMatcher();

        /**
         * @var $psrFactory PsrHttpFactory
         */
        $psrFactory = $this->getContainer()->get(PsrHttpFactory::class);

        $route = $matcher->match($psrFactory->createRequest($request));

        if (! $route) {
            // get the first of the best-available non-matched routes
            $failedRoute = $matcher->getFailedRoute();

            // which matching rule failed?
            switch ($failedRoute->failedRule) {
                case Allows::class:
                    $message = sprintf(
                        'No route found for "%s %s": Method Not Allowed (Allow: %s)',
                        $request->getMethod(),
                        $request->getUriForPath($request->getPathInfo()),
                        implode(', ', $failedRoute->allows)
                    );

                    throw new MethodNotAllowedHttpException($failedRoute->allows, $message);
                case Accepts::class:
                    $message = sprintf(
                        'No route found for "%s %s": Unacceptable request (Accepts: %s)',
                        $request->getMethod(),
                        $request->getUriForPath($request->getPathInfo()),
                        implode(', ', $failedRoute->accepts)
                    );
                    throw new BadRequestHttpException($message);
                default:
                    $message = sprintf(
                        'No route found for "%s %s"',
                        $request->getMethod(),
                        $request->getUriForPath($request->getPathInfo())
                    );

                    if ($referer = $request->headers->get('referer')) {
                        $message .= sprintf(' (from "%s")', $referer);
                    }

                    throw new NotFoundHttpException($message);
            }
        }

        $request->attributes->add($route->attributes);
        $request->attributes->set('_controller', $route->handler);
    }
}
