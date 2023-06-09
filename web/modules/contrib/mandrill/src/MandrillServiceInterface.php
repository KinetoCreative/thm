<?php
declare(strict_types=1);

namespace Drupal\mandrill;

/**
 * Interface for the Mandrill service.
 */
interface MandrillServiceInterface {
  public function getMailSystems();
  public function getReceivers($receiver);
  public function send($message);
}
