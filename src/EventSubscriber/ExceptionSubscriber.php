<?php

namespace App\EventSubscriber;

use App\Exception\RequestStructureException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundHttpException) {
            $data = [
            'status' => $exception->getStatusCode(),
            'message' => 'Resource not found'
            ];
        } elseif ($exception instanceof RequestStructureException) {
            $data = [
            'status' => $exception->getStatusCode(),
            'message' => json_decode($exception->getMessage())
            ];
        } elseif ($exception instanceof HttpException) {
            $data = [
            'status' => $exception->getStatusCode(),
            'message' => $exception->getMessage()
            ];
        } else {
            $data = [
            'status' => 500,
            'message' => $exception->getMessage()
            ];
        }

        $response = new JsonResponse(($data['message']), $data['status']);

        $event->setResponse($response);
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.exception' => 'onKernelException',
        ];
    }
}
