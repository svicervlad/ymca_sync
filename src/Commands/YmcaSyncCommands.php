<?php

namespace Drupal\ymca_sync\Commands;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;
use Drush\Commands\DrushCommands;

/**
 * A Drush commandfile.
 *
 * In addition to this file, you need a drush.services.yml
 * in root of your module, and a composer.json file that provides the name
 * of the services file to use.
 *
 * See these files for an example of injecting Drupal services:
 *   - http://cgit.drupalcode.org/devel/tree/src/Commands/DevelCommands.php
 *   - http://cgit.drupalcode.org/devel/tree/drush.services.yml
 */
class YmcaSyncCommands extends DrushCommands {

  /**
   * Command for run ymca syncers.
   *
   * @param string $syncer
   *   Name of syncer, you can find syncer by run yn-sync:list.
   * @param array $options
   *   An associative array of options for syncers.
   *
   * @usage yn-sync syncer.name
   *   Usage description
   *
   * @command yn-sync:sync
   * @aliases yn-sync
   */
  public function sync($syncer, array $options = ['mode' => 'default']) {
    $syncers = \Drupal::service('ymca_sync.sync_repository')->getSyncers();
    if (!in_array($syncer, $syncers)) {
      $this->logger()->info(sprintf('Syncer %s not exist.', $syncer));
      return;
    }
    $this->logger()->notice(sprintf('Try to start syncer %s', $syncer));
    \Drupal::service('ymca_sync.syncer')->run($syncer, "proceed", $options);
  }

  /**
   * List of YMCA Syncers.
   *
   * @usage yn-sync:list
   *   Return list of availible syncers
   *
   * @command yn-sync:list
   * @aliases yn-sync:list,yn-sync-list
   */
  public function list() {
    /** @var \Drupal\Core\Config\ConfigFactory $configFactory */
    $configFactory = \Drupal::service('config.factory');
    $activeSyncers = $configFactory->get('ymca_sync.settings')->get('active_syncers');
    $syncers = \Drupal::service('ymca_sync.sync_repository')->getSyncers();
    $result = [];
    foreach ($syncers as $syncer) {
      $result[] = [
        'active' => in_array($syncer, $activeSyncers) ? 'active' : 'disable',
        'syncer' => $syncer,
      ];
    }
    return new RowsOfFields($result);
  }

}
