<?php

/**
 * @file
 * Contains sendgrid_campaign.page.inc.
 *
 * Page callback for Sendgrid campaign entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Sendgrid campaign templates.
 *
 * Default template: sendgrid_campaign.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_sendgrid_campaign(array &$variables) {
  // Fetch SendgridCampaign Entity Object.
  $sendgrid_campaign = $variables['elements']['#sendgrid_campaign'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
