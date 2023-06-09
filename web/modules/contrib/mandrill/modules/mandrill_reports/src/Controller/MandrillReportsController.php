<?php
declare(strict_types=1);

/**
 * @file
 * Contains \Drupal\mandrill_reports\Controller\MandrillReportsController.
 */

namespace Drupal\mandrill_reports\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * MandrillReports controller.
 */
class MandrillReportsController extends ControllerBase {

  /**
   * View Mandrill dashboard report.
   *
   * @return array
   *   Renderable array of page content.
   */
  public function dashboard(): array {
    $content = [];

    /* @var $reports \Drupal\mandrill_reports\MandrillReportsService */
    $reports = \Drupal::service('mandrill_reports.service');

    $data = [];

    $data['user'] = $reports->getUser();
    $data['all_time_series'] = $reports->getTagsAllTimeSeries();

    $settings = [];
    // All time series chart data.
    foreach ($data['all_time_series'] as $series) {
      $settings['mandrill_reports']['volume'][] = [
        'date' => $series->time,
        'sent' => $series->sent,
        'bounced' => $series->hard_bounces + $series->soft_bounces,
        'rejected' => $series->rejects,
      ];

      $settings['mandrill_reports']['engagement'][] = [
        'date' => $series->time,
        'open_rate' => $series->sent == 0 ? 0 : $series->unique_opens / $series->sent,
        'click_rate' => $series->sent == 0 ? 0 : $series->unique_clicks / $series->sent,
      ];
    }

    $content['info'] = [
      '#markup' => t(
        'The following reports are based on Mandrill data from the last 30 days. For more comprehensive data, please visit your %dashboard. %cache to ensure the data is current.',
        [
          '%dashboard' => Link::fromTextAndUrl(t('Mandrill Dashboard'), Url::fromUri('https://mandrillapp.com/'))->toString(),
          '%cache' => Link::fromTextAndUrl(t('Clear your cache'), Url::fromRoute('system.performance_settings'))->toString(),
        ]
      ),
    ];

    $content['volume'] = [
      '#prefix' => '<h2>' . t('Sending Volume') . '</h2>',
      '#markup' => '<div id="mandrill-volume-chart"></div>',
    ];

    $content['engagement'] = [
      '#prefix' => '<h2>' . t('Average Open and Click Rate') . '</h2>',
      '#markup' => '<div id="mandrill-engage-chart"></div>',
    ];

    $content['#attached']['library'][] = 'mandrill_reports/google-jsapi';
    $content['#attached']['library'][] = 'mandrill_reports/reports-stats';

    $content['#attached']['drupalSettings'] = $settings;

    return $content;
  }

  /**
   * View Mandrill account summary report.
   *
   * @return array
   *   Renderable array of page content.
   */
  public function summary() {
    $content = [];

    /* @var $reports \Drupal\mandrill_reports\MandrillReportsService */
    $reports = \Drupal::service('mandrill_reports.service');

    $user = $reports->getUser();

    $content['info_table_desc'] = [
      '#markup' => t('Active API user information.'),
    ];

    // User info table.
    $content['info_table'] = [
      '#type' => 'table',
      '#header' => [
        t('Attribute'),
        t('Value'),
      ],
      '#empty' => 'No account information.',
    ];

    $info = [
      ['attr' => t('Username'), 'value' => $user->username],
      ['attr' => t('Reputation'), 'value' => $user->reputation],
      ['attr' => t('Hourly quota'), 'value' => $user->hourly_quota],
      ['attr' => t('Backlog'), 'value' => $user->backlog],
    ];

    $row = 0;
    foreach ($info as $item) {
      $content['info_table'][$row]['attribute'] = [
        '#markup' => $item['attr'],
      ];

      $content['info_table'][$row]['value'] = [
        '#markup' => $item['value'],
      ];

      $row++;
    }

    $content['stats_table_desc'] = [
      '#markup' => t('This table contains an aggregate summary of the account\'s sending stats.'),
    ];

    // User stats table.
    $content['stats_table'] = [
      '#type' => 'table',
      '#header' => [
        t('Range'),
        t('Sent'),
        t('hard_bounces'),
        t('soft_bounces'),
        t('Rejects'),
        t('Complaints'),
        t('Unsubs'),
        t('Opens'),
        t('unique_opens'),
        t('Clicks'),
        t('unique_clicks'),
      ],
      '#empty' => 'No account activity yet.',
    ];

    if (!empty($user->stats)) {
      $row = 0;
      foreach ($user->stats as $key => $stat) {
        $content['stats_table'][$row]['range'] = [
          '#markup' => str_replace('_', ' ', $key),
        ];

        $content['stats_table'][$row]['sent'] = [
          '#markup' => $stat->sent,
        ];

        $content['stats_table'][$row]['hard_bounces'] = [
          '#markup' => $stat->hard_bounces,
        ];

        $content['stats_table'][$row]['soft_bounces'] = [
          '#markup' => $stat->soft_bounces,
        ];

        $content['stats_table'][$row]['rejects'] = [
          '#markup' => $stat->rejects,
        ];

        $content['stats_table'][$row]['complaints'] = [
          '#markup' => $stat->complaints,
        ];

        $content['stats_table'][$row]['unsubs'] = [
          '#markup' => $stat->unsubs,
        ];

        $content['stats_table'][$row]['opens'] = [
          '#markup' => $stat->opens,
        ];

        $content['stats_table'][$row]['unique_opens'] = [
          '#markup' => $stat->unique_opens,
        ];

        $content['stats_table'][$row]['clicks'] = [
          '#markup' => $stat->clicks,
        ];

        $content['stats_table'][$row]['unique_clicks'] = [
          '#markup' => $stat->unique_clicks,
        ];

        $row++;
      }
    }

    return $content;
  }

}
