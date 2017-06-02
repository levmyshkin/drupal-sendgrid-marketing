<?php

namespace Drupal\sendgrid_marketing\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Drupal\Core\Url;

class SendgridMarketingSubscriber implements EventSubscriberInterface {

  public function checkRedirection(FilterResponseEvent $event) {
    $route = \Drupal::routeMatch()->getRouteName();
    if ($route == 'entity.sendgrid_campaign.canonical') {
      $url = Url::fromRoute('sendgrid_marketing.sendgrid_campaigns_list');
      $response = new RedirectResponse($url->setAbsolute()->toString());
      $event->setResponse($response);
    }
  }

  static function getSubscribedEvents() {
    $events[KernelEvents::RESPONSE][] = ['checkRedirection'];
    return $events;
  }
}