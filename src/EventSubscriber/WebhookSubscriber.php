<?php


namespace App\EventSubscriber;


use DocusignBundle\Events\AuthenticationFailedEvent;
use DocusignBundle\Events\AutoRespondedEvent;
use DocusignBundle\Events\CompletedEvent;
use DocusignBundle\Events\DeclinedEvent;
use DocusignBundle\Events\DeliveredEvent;
use DocusignBundle\Events\SentEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WebhookSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        // return the subscribed events, their methods and priorities
        return [
            AuthenticationFailedEvent::class => 'onAuthenticationFailed',
            AutoRespondedEvent::class => 'onAutoResponded',
            CompletedEvent::class => 'onCompleted',
            DeclinedEvent::class => 'onDeclined',
            DeliveredEvent::class => 'onDelivered',
            SentEvent::class => 'onSent',
        ];
    }

    public function onAuthenticationFailed()
    {
    }

    public function onAutoResponded()
    {
    }

    public function onCompleted()
    {
    }

    public function onDeclined()
    {
    }

    public function onDelivered()
    {
    }

    public function onSent()
    {
    }
}