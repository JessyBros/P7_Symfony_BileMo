<?php 
namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HttpExceptionInterface) {
            $code = $exception->getStatusCode();
        } else {
            $code = $exception->getCode();
        }

        $message = json_encode(
            [
                "message" =>$exception->getMessage(),
                "code" => $code
            ]
        );

        $response = new JsonResponse($message, $code, [], true);

        $event->setResponse($response);
    }
}