<?php

/**
 * @file
 * Example Metatag v1 configuration.
 */

/**
 * Notes on how to use this file.
 * - When adding tests for changes to meta tags provided by a submodule, that
 *   submodule must be listed in the modules list below.
 * - It is easiest to not add meta tag default configuration changes here that
 *   depend upon submodules, it works better to make those changes in the
 *   appropriate update script.
 * - There is currently only one Metatag field defined, on the Article content
 *   type.
 * - Each meta tag value to be tested is added to the fields lower down.
 *
 * @todo Finish documenting this file.
 * @todo Expand to handle multiple languages.
 * @todo Expand to handle revisions.
 * @todo Expand to have Metatag fields on multiple entity types.
 * @todo Expand to have multiple Metatag fields, with different field names.
 * @todo Work out a better way of handling field specification changes.
 */

use Drupal\Core\Database\Database;
use Drupal\Component\Serialization\Yaml;
use Drupal\Component\Uuid\Php as Uuid;

$config_fields = ['collection', 'name', 'data'];
$keyvalue_fields = ['collection', 'name', 'value'];

$connection = Database::getConnection();

// Enable Metatag (and Token).
$extensions = $connection->select('config')
  ->fields('config', ['data'])
  ->condition('collection', '')
  ->condition('name', 'core.extension')
  ->execute()
  ->fetchField();
$extensions = unserialize($extensions, ['allowed_classes' => []]);
$extensions['module']['metatag'] = 0;
/**
 * Additional submodules must be added here if their meta tags are being tested.
 */
$extensions['module']['metatag_google_plus'] = 0;
$extensions['module']['metatag_twitter_cards'] = 0;
$extensions['module']['token'] = 0;
$connection->update('config')
  ->fields(['data' => serialize($extensions)])
  ->condition('collection', '')
  ->condition('name', 'core.extension')
  ->execute();

// Schema configuration for the two modules.
$connection->insert('key_value')
  ->fields($keyvalue_fields)
  ->values([
    'collection' => 'system.schema',
    'name' => 'metatag',
    'value' => 'i:8109;',
  ])
  ->values([
    'collection' => 'system.schema',
    'name' => 'token',
    'value' => 'i:8000;',
  ])
  ->execute();

// Indicate that the Metatag post_update scripts had already executed.
$data = $connection->select('key_value')
  ->fields('key_value', ['value'])
  ->condition('collection', 'post_update')
  ->condition('name', 'existing_updates')
  ->execute()
  ->fetchField();
$data = unserialize($data, ['allowed_classes' => []]);
$data[] = 'metatag_post_update_convert_author_config';
$data[] = 'metatag_post_update_convert_author_data';
$data[] = 'metatag_post_update_convert_mask_icon_to_array_values';
$connection->update('key_value')
  ->fields(['value' => serialize($data)])
  ->condition('collection', 'post_update')
  ->condition('name', 'existing_updates')
  ->execute();

// Load Token configuration.
$connection->insert('key_value')
  ->fields($keyvalue_fields)
  ->values([
    'collection' => '',
    'name' => 'core.entity_view_mode.node.token',
    // @todo Convert this to an array that's passed through serialize().
    'value' => 'a:8:{s:4:"uuid";s:36:"8e09c5fa-e94f-440c-9650-68e32e973444";s:8:"langcode";s:2:"en";s:6:"status";b:1;s:12:"dependencies";a:1:{s:6:"module";a:1:{i:0;s:4:"node";}}s:2:"id";s:10:"node.token";s:5:"label";s:5:"Token";s:16:"targetEntityType";s:4:"node";s:5:"cache";b:1;}',
  ])
  ->execute();

// Metatag configuration.
// @todo Load additional configurations.
$connection->insert('key_value')
  ->fields($keyvalue_fields)
  ->values([
    'collection' => 'config.entity.key_store.entity_view_mode',
    'name' => 'uuid:8e09c5fa-e94f-440c-9650-68e32e973444',
    'value' => serialize(['core.entity_view_mode.node.token']),
  ])
  ->values([
    'collection' => 'config.entity.key_store.metatag_defaults',
    'name' => 'uuid:6185b80a-8c5a-4a87-a73d-895a278ad83c',
    'value' => serialize(['metatag.metatag_defaults.global']),
  ])
  ->values([
    'collection' => 'config.entity.key_store.metatag_defaults',
    'name' => 'uuid:b6f8083d-a2b4-4555-9b65-eab2c1eb2b9f',
    'value' => serialize(['metatag.metatag_defaults.node']),
  ])
  ->values([
    'collection' => 'entity.definitions.installed',
    'name' => 'metatag_defaults.entity_type',
    // @todo Convert this to an object that's passed through serialize().
    'value' => 'O:42:"Drupal\Core\Config\Entity\ConfigEntityType":43:{s:16:" * config_prefix";s:16:"metatag_defaults";s:15:" * static_cache";b:0;s:14:" * lookup_keys";a:1:{i:0;s:4:"uuid";}s:16:" * config_export";a:3:{i:0;s:2:"id";i:1;s:5:"label";i:2;s:4:"tags";}s:21:" * mergedConfigExport";a:0:{}s:15:" * render_cache";b:1;s:19:" * persistent_cache";b:1;s:14:" * entity_keys";a:8:{s:2:"id";s:2:"id";s:5:"label";s:5:"label";s:8:"revision";s:0:"";s:6:"bundle";s:0:"";s:8:"langcode";s:8:"langcode";s:16:"default_langcode";s:16:"default_langcode";s:29:"revision_translation_affected";s:29:"revision_translation_affected";s:4:"uuid";s:4:"uuid";}s:5:" * id";s:16:"metatag_defaults";s:16:" * originalClass";s:37:"Drupal\metatag\Entity\MetatagDefaults";s:11:" * handlers";a:4:{s:12:"list_builder";s:41:"Drupal\metatag\MetatagDefaultsListBuilder";s:4:"form";a:4:{s:3:"add";s:39:"Drupal\metatag\Form\MetatagDefaultsForm";s:4:"edit";s:39:"Drupal\metatag\Form\MetatagDefaultsForm";s:6:"delete";s:45:"Drupal\metatag\Form\MetatagDefaultsDeleteForm";s:6:"revert";s:45:"Drupal\metatag\Form\MetatagDefaultsRevertForm";}s:6:"access";s:45:"Drupal\Core\Entity\EntityAccessControlHandler";s:7:"storage";s:45:"Drupal\Core\Config\Entity\ConfigEntityStorage";}s:19:" * admin_permission";s:20:"administer meta tags";s:25:" * permission_granularity";s:11:"entity_type";s:8:" * links";a:4:{s:9:"edit-form";s:52:"/admin/config/search/metatag/{metatag_defaults}/edit";s:11:"delete-form";s:54:"/admin/config/search/metatag/{metatag_defaults}/delete";s:11:"revert-form";s:54:"/admin/config/search/metatag/{metatag_defaults}/revert";s:10:"collection";s:28:"/admin/config/search/metatag";}s:21:" * bundle_entity_type";N;s:12:" * bundle_of";N;s:15:" * bundle_label";N;s:13:" * base_table";N;s:22:" * revision_data_table";N;s:17:" * revision_table";N;s:13:" * data_table";N;s:11:" * internal";b:0;s:15:" * translatable";b:0;s:19:" * show_revision_ui";b:0;s:8:" * label";O:48:"Drupal\Core\StringTranslation\TranslatableMarkup":3:{s:9:" * string";s:16:"Metatag defaults";s:12:" * arguments";a:0:{}s:10:" * options";a:0:{}}s:19:" * label_collection";s:0:"";s:17:" * label_singular";s:0:"";s:15:" * label_plural";s:0:"";s:14:" * label_count";a:0:{}s:15:" * uri_callback";N;s:8:" * group";s:13:"configuration";s:14:" * group_label";O:48:"Drupal\Core\StringTranslation\TranslatableMarkup":3:{s:9:" * string";s:13:"Configuration";s:12:" * arguments";a:0:{}s:10:" * options";a:1:{s:7:"context";s:17:"Entity type group";}}s:22:" * field_ui_base_route";N;s:26:" * common_reference_target";b:0;s:22:" * list_cache_contexts";a:0:{}s:18:" * list_cache_tags";a:1:{i:0;s:28:"config:metatag_defaults_list";}s:14:" * constraints";a:0:{}s:13:" * additional";a:1:{s:10:"token_type";s:16:"metatag_defaults";}s:8:" * class";s:37:"Drupal\metatag\Entity\MetatagDefaults";s:11:" * provider";s:7:"metatag";s:14:" * _serviceIds";a:0:{}s:18:" * _entityStorages";a:0:{}s:20:" * stringTranslation";N;}',
  ])
  ->execute();

$config = Yaml::decode(file_get_contents(__DIR__ . '/../../config/install/metatag.metatag_defaults.global.yml'));
// Need to hardcode a UUID value to avoid problems with the config system.
$config['uuid'] = (new Uuid())->generate();
$connection->insert('config')
  ->fields($config_fields)
  ->values([
    'collection' => '',
    'name' => 'metatag.metatag_defaults.global',
    'data' => serialize($config),
  ])
  ->execute();

// Node configuration.
$config = Yaml::decode(file_get_contents(__DIR__ . '/../../config/install/metatag.metatag_defaults.node.yml'));
// Need to hardcode a UUID value to avoid problems with the config system.
$config['uuid'] = (new Uuid())->generate();
$connection->insert('config')
  ->fields($config_fields)
  ->values([
    'collection' => '',
    'name' => 'metatag.metatag_defaults.node',
    'data' => serialize($config),
  ])
  ->execute();

// Create a field on the Article content type.
$connection->insert('config')
  ->fields($config_fields)
  ->values([
    'collection' => '',
    'name' => 'field.field.node.article.field_meta_tags',
    // @todo Convert this to an array that's passed through serialize().
    'data' => 'a:16:{s:4:"uuid";s:36:"109353f9-c0f7-4e30-a1a7-b7f8ebaa940d";s:8:"langcode";s:2:"en";s:6:"status";b:1;s:12:"dependencies";a:2:{s:6:"config";a:2:{i:0;s:34:"field.storage.node.field_meta_tags";i:1;s:17:"node.type.article";}s:6:"module";a:1:{i:0;s:7:"metatag";}}s:2:"id";s:28:"node.article.field_meta_tags";s:10:"field_name";s:15:"field_meta_tags";s:11:"entity_type";s:4:"node";s:6:"bundle";s:7:"article";s:5:"label";s:9:"Meta tags";s:11:"description";s:0:"";s:8:"required";b:0;s:12:"translatable";b:0;s:13:"default_value";a:0:{}s:22:"default_value_callback";s:0:"";s:8:"settings";a:0:{}s:10:"field_type";s:7:"metatag";}',
  ])
  ->values([
    'collection' => '',
    'name' => 'field.storage.node.field_meta_tags',
    // @todo Convert this to an array that's passed through serialize().
    'data' => 'a:16:{s:4:"uuid";s:36:"6aaab457-3728-4319-afa3-938e753ed342";s:8:"langcode";s:2:"en";s:6:"status";b:1;s:12:"dependencies";a:1:{s:6:"module";a:2:{i:0;s:7:"metatag";i:1;s:4:"node";}}s:2:"id";s:20:"node.field_meta_tags";s:10:"field_name";s:15:"field_meta_tags";s:11:"entity_type";s:4:"node";s:4:"type";s:7:"metatag";s:8:"settings";a:0:{}s:6:"module";s:7:"metatag";s:6:"locked";b:0;s:11:"cardinality";i:1;s:12:"translatable";b:1;s:7:"indexes";a:0:{}s:22:"persist_with_no_fields";b:0;s:14:"custom_storage";b:0;}',
  ])
  ->execute();

$connection->insert('key_value')
  ->fields($keyvalue_fields)
  ->values([
    'collection' => 'config.entity.key_store.field_config',
    'name' => 'uuid:109353f9-c0f7-4e30-a1a7-b7f8ebaa940d',
    'value' => serialize(['field.field.node.article.field_meta_tags']),
  ])
  ->values([
    'collection' => 'config.entity.key_store.field_storage_config',
    'name' => 'uuid:6aaab457-3728-4319-afa3-938e753ed342',
    'value' => serialize(['field.storage.node.field_meta_tags']),
  ])
  ->values([
    'collection' => 'entity.storage_schema.sql',
    'name' => 'node.field_schema_data.field_meta_tags',
    // @todo Convert this to an array that's passed through serialize().
    'value' => 'a:2:{s:21:"node__field_meta_tags";a:4:{s:11:"description";s:44:"Data storage for node field field_meta_tags.";s:6:"fields";a:7:{s:6:"bundle";a:5:{s:4:"type";s:13:"varchar_ascii";s:6:"length";i:128;s:8:"not null";b:1;s:7:"default";s:0:"";s:11:"description";s:88:"The field instance bundle to which this row belongs, used when deleting a field instance";}s:7:"deleted";a:5:{s:4:"type";s:3:"int";s:4:"size";s:4:"tiny";s:8:"not null";b:1;s:7:"default";i:0;s:11:"description";s:60:"A boolean indicating whether this data item has been deleted";}s:9:"entity_id";a:4:{s:4:"type";s:3:"int";s:8:"unsigned";b:1;s:8:"not null";b:1;s:11:"description";s:38:"The entity id this data is attached to";}s:11:"revision_id";a:4:{s:4:"type";s:3:"int";s:8:"unsigned";b:1;s:8:"not null";b:1;s:11:"description";s:47:"The entity revision id this data is attached to";}s:8:"langcode";a:5:{s:4:"type";s:13:"varchar_ascii";s:6:"length";i:32;s:8:"not null";b:1;s:7:"default";s:0:"";s:11:"description";s:37:"The language code for this data item.";}s:5:"delta";a:4:{s:4:"type";s:3:"int";s:8:"unsigned";b:1;s:8:"not null";b:1;s:11:"description";s:67:"The sequence number for this data item, used for multi-value fields";}s:21:"field_meta_tags_value";a:3:{s:4:"type";s:4:"text";s:4:"size";s:3:"big";s:8:"not null";b:1;}}s:11:"primary key";a:4:{i:0;s:9:"entity_id";i:1;s:7:"deleted";i:2;s:5:"delta";i:3;s:8:"langcode";}s:7:"indexes";a:2:{s:6:"bundle";a:1:{i:0;s:6:"bundle";}s:11:"revision_id";a:1:{i:0;s:11:"revision_id";}}}s:30:"node_revision__field_meta_tags";a:4:{s:11:"description";s:56:"Revision archive storage for node field field_meta_tags.";s:6:"fields";a:7:{s:6:"bundle";a:5:{s:4:"type";s:13:"varchar_ascii";s:6:"length";i:128;s:8:"not null";b:1;s:7:"default";s:0:"";s:11:"description";s:88:"The field instance bundle to which this row belongs, used when deleting a field instance";}s:7:"deleted";a:5:{s:4:"type";s:3:"int";s:4:"size";s:4:"tiny";s:8:"not null";b:1;s:7:"default";i:0;s:11:"description";s:60:"A boolean indicating whether this data item has been deleted";}s:9:"entity_id";a:4:{s:4:"type";s:3:"int";s:8:"unsigned";b:1;s:8:"not null";b:1;s:11:"description";s:38:"The entity id this data is attached to";}s:11:"revision_id";a:4:{s:4:"type";s:3:"int";s:8:"unsigned";b:1;s:8:"not null";b:1;s:11:"description";s:47:"The entity revision id this data is attached to";}s:8:"langcode";a:5:{s:4:"type";s:13:"varchar_ascii";s:6:"length";i:32;s:8:"not null";b:1;s:7:"default";s:0:"";s:11:"description";s:37:"The language code for this data item.";}s:5:"delta";a:4:{s:4:"type";s:3:"int";s:8:"unsigned";b:1;s:8:"not null";b:1;s:11:"description";s:67:"The sequence number for this data item, used for multi-value fields";}s:21:"field_meta_tags_value";a:3:{s:4:"type";s:4:"text";s:4:"size";s:3:"big";s:8:"not null";b:1;}}s:11:"primary key";a:5:{i:0;s:9:"entity_id";i:1;s:11:"revision_id";i:2;s:7:"deleted";i:3;s:5:"delta";i:4;s:8:"langcode";}s:7:"indexes";a:2:{s:6:"bundle";a:1:{i:0;s:6:"bundle";}s:11:"revision_id";a:1:{i:0;s:11:"revision_id";}}}}',
  ])
  ->execute();

// @todo Update these key_value definitions.
//  ->values([
//    'collection' => '',
//    'name' => 'core.entity_form_display.node.article.default',
// -  'data' => 'a:10:{s:4:"uuid";s:36:"6cd7cc93-f55f-4ce6-be10-161a3d8113b4";s:8:"langcode";s:2:"en";s:6:"status";b:1;s:12:"dependencies";a:2:{s:6:"config";a:6:{i:0;s:29:"field.field.node.article.body";i:1;s:32:"field.field.node.article.comment";i:2;s:36:"field.field.node.article.field_image";i:3;s:35:"field.field.node.article.field_tags";i:4;s:21:"image.style.thumbnail";i:5;s:17:"node.type.article";}s:6:"module";a:4:{i:0;s:7:"comment";i:1;s:5:"image";i:2;s:4:"path";i:3;s:4:"text";}}s:2:"id";s:20:"node.article.default";s:16:"targetEntityType";s:4:"node";s:6:"bundle";s:7:"article";s:4:"mode";s:7:"default";s:7:"content";a:11:{s:4:"body";a:5:{s:4:"type";s:26:"text_textarea_with_summary";s:6:"weight";i:1;s:6:"region";s:7:"content";s:8:"settings";a:4:{s:4:"rows";i:9;s:12:"summary_rows";i:3;s:11:"placeholder";s:0:"";s:12:"show_summary";b:0;}s:20:"third_party_settings";a:0:{}}s:7:"comment";a:5:{s:4:"type";s:15:"comment_default";s:6:"weight";i:20;s:6:"region";s:7:"content";s:8:"settings";a:0:{}s:20:"third_party_settings";a:0:{}}s:7:"created";a:5:{s:4:"type";s:18:"datetime_timestamp";s:6:"weight";i:10;s:6:"region";s:7:"content";s:8:"settings";a:0:{}s:20:"third_party_settings";a:0:{}}s:11:"field_image";a:5:{s:4:"type";s:11:"image_image";s:6:"weight";i:4;s:6:"region";s:7:"content";s:8:"settings";a:2:{s:18:"progress_indicator";s:8:"throbber";s:19:"preview_image_style";s:9:"thumbnail";}s:20:"third_party_settings";a:0:{}}s:10:"field_tags";a:5:{s:4:"type";s:34:"entity_reference_autocomplete_tags";s:6:"weight";i:3;s:6:"region";s:7:"content";s:8:"settings";a:4:{s:14:"match_operator";s:8:"CONTAINS";s:11:"match_limit";i:10;s:4:"size";i:60;s:11:"placeholder";s:0:"";}s:20:"third_party_settings";a:0:{}}s:4:"path";a:5:{s:4:"type";s:4:"path";s:6:"weight";i:30;s:6:"region";s:7:"content";s:8:"settings";a:0:{}s:20:"third_party_settings";a:0:{}}s:7:"promote";a:5:{s:4:"type";s:16:"boolean_checkbox";s:6:"weight";i:15;s:6:"region";s:7:"content";s:8:"settings";a:1:{s:13:"display_label";b:1;}s:20:"third_party_settings";a:0:{}}s:6:"status";a:5:{s:4:"type";s:16:"boolean_checkbox";s:6:"weight";i:121;s:6:"region";s:7:"content";s:8:"settings";a:1:{s:13:"display_label";b:1;}s:20:"third_party_settings";a:0:{}}s:6:"sticky";a:5:{s:4:"type";s:16:"boolean_checkbox";s:6:"weight";i:16;s:6:"region";s:7:"content";s:8:"settings";a:1:{s:13:"display_label";b:1;}s:20:"third_party_settings";a:0:{}}s:5:"title";a:5:{s:4:"type";s:16:"string_textfield";s:6:"weight";i:0;s:6:"region";s:7:"content";s:8:"settings";a:2:{s:4:"size";i:60;s:11:"placeholder";s:0:"";}s:20:"third_party_settings";a:0:{}}s:3:"uid";a:5:{s:4:"type";s:29:"entity_reference_autocomplete";s:6:"weight";i:5;s:6:"region";s:7:"content";s:8:"settings";a:4:{s:14:"match_operator";s:8:"CONTAINS";s:11:"match_limit";i:10;s:4:"size";i:60;s:11:"placeholder";s:0:"";}s:20:"third_party_settings";a:0:{}}}s:6:"hidden";a:0:{}}',
// +  'data' => 'a:10:{s:4:"uuid";s:36:"6cd7cc93-f55f-4ce6-be10-161a3d8113b4";s:8:"langcode";s:2:"en";s:6:"status";b:1;s:12:"dependencies";a:2:{s:6:"config";a:7:{i:0;s:29:"field.field.node.article.body";i:1;s:32:"field.field.node.article.comment";i:2;s:36:"field.field.node.article.field_image";i:3;s:40:"field.field.node.article.field_meta_tags";i:4;s:35:"field.field.node.article.field_tags";i:5;s:21:"image.style.thumbnail";i:6;s:17:"node.type.article";}s:6:"module";a:5:{i:0;s:7:"comment";i:1;s:5:"image";i:2;s:7:"metatag";i:3;s:4:"path";i:4;s:4:"text";}}s:2:"id";s:20:"node.article.default";s:16:"targetEntityType";s:4:"node";s:6:"bundle";s:7:"article";s:4:"mode";s:7:"default";s:7:"content";a:12:{s:4:"body";a:5:{s:4:"type";s:26:"text_textarea_with_summary";s:6:"weight";i:1;s:6:"region";s:7:"content";s:8:"settings";a:4:{s:4:"rows";i:9;s:12:"summary_rows";i:3;s:11:"placeholder";s:0:"";s:12:"show_summary";b:0;}s:20:"third_party_settings";a:0:{}}s:7:"comment";a:5:{s:4:"type";s:15:"comment_default";s:6:"weight";i:20;s:6:"region";s:7:"content";s:8:"settings";a:0:{}s:20:"third_party_settings";a:0:{}}s:7:"created";a:5:{s:4:"type";s:18:"datetime_timestamp";s:6:"weight";i:10;s:6:"region";s:7:"content";s:8:"settings";a:0:{}s:20:"third_party_settings";a:0:{}}s:11:"field_image";a:5:{s:4:"type";s:11:"image_image";s:6:"weight";i:4;s:6:"region";s:7:"content";s:8:"settings";a:2:{s:18:"progress_indicator";s:8:"throbber";s:19:"preview_image_style";s:9:"thumbnail";}s:20:"third_party_settings";a:0:{}}s:15:"field_meta_tags";a:5:{s:4:"type";s:16:"metatag_firehose";s:6:"weight";i:122;s:6:"region";s:7:"content";s:8:"settings";a:2:{s:7:"sidebar";b:1;s:11:"use_details";b:1;}s:20:"third_party_settings";a:0:{}}s:10:"field_tags";a:5:{s:4:"type";s:34:"entity_reference_autocomplete_tags";s:6:"weight";i:3;s:6:"region";s:7:"content";s:8:"settings";a:4:{s:14:"match_operator";s:8:"CONTAINS";s:11:"match_limit";i:10;s:4:"size";i:60;s:11:"placeholder";s:0:"";}s:20:"third_party_settings";a:0:{}}s:4:"path";a:5:{s:4:"type";s:4:"path";s:6:"weight";i:30;s:6:"region";s:7:"content";s:8:"settings";a:0:{}s:20:"third_party_settings";a:0:{}}s:7:"promote";a:5:{s:4:"type";s:16:"boolean_checkbox";s:6:"weight";i:15;s:6:"region";s:7:"content";s:8:"settings";a:1:{s:13:"display_label";b:1;}s:20:"third_party_settings";a:0:{}}s:6:"status";a:5:{s:4:"type";s:16:"boolean_checkbox";s:6:"weight";i:121;s:6:"region";s:7:"content";s:8:"settings";a:1:{s:13:"display_label";b:1;}s:20:"third_party_settings";a:0:{}}s:6:"sticky";a:5:{s:4:"type";s:16:"boolean_checkbox";s:6:"weight";i:16;s:6:"region";s:7:"content";s:8:"settings";a:1:{s:13:"display_label";b:1;}s:20:"third_party_settings";a:0:{}}s:5:"title";a:5:{s:4:"type";s:16:"string_textfield";s:6:"weight";i:0;s:6:"region";s:7:"content";s:8:"settings";a:2:{s:4:"size";i:60;s:11:"placeholder";s:0:"";}s:20:"third_party_settings";a:0:{}}s:3:"uid";a:5:{s:4:"type";s:29:"entity_reference_autocomplete";s:6:"weight";i:5;s:6:"region";s:7:"content";s:8:"settings";a:4:{s:14:"match_operator";s:8:"CONTAINS";s:11:"match_limit";i:10;s:4:"size";i:60;s:11:"placeholder";s:0:"";}s:20:"third_party_settings";a:0:{}}}s:6:"hidden";a:0:{}}',
//  ])
//  ->values([
//    'collection' => '',
//    'name' => 'core.entity_view_display.node.article.default',
// -  'data' => 'a:10:{s:4:"uuid";s:36:"684de49b-f163-4427-9700-af1a48323f3e";s:8:"langcode";s:2:"en";s:6:"status";b:1;s:12:"dependencies";a:2:{s:6:"config";a:7:{i:0;s:48:"core.entity_view_display.comment.comment.default";i:1;s:29:"field.field.node.article.body";i:2;s:32:"field.field.node.article.comment";i:3;s:36:"field.field.node.article.field_image";i:4;s:35:"field.field.node.article.field_tags";i:5;s:17:"image.style.large";i:6;s:17:"node.type.article";}s:6:"module";a:4:{i:0;s:7:"comment";i:1;s:5:"image";i:2;s:4:"text";i:3;s:4:"user";}}s:2:"id";s:20:"node.article.default";s:16:"targetEntityType";s:4:"node";s:6:"bundle";s:7:"article";s:4:"mode";s:7:"default";s:7:"content";a:5:{s:4:"body";a:6:{s:4:"type";s:12:"text_default";s:5:"label";s:6:"hidden";s:8:"settings";a:0:{}s:20:"third_party_settings";a:0:{}s:6:"weight";i:0;s:6:"region";s:7:"content";}s:7:"comment";a:6:{s:4:"type";s:15:"comment_default";s:5:"label";s:5:"above";s:8:"settings";a:2:{s:9:"view_mode";s:7:"default";s:8:"pager_id";i:0;}s:20:"third_party_settings";a:0:{}s:6:"weight";i:20;s:6:"region";s:7:"content";}s:11:"field_image";a:6:{s:4:"type";s:5:"image";s:5:"label";s:6:"hidden";s:8:"settings";a:3:{s:10:"image_link";s:0:"";s:11:"image_style";s:5:"large";s:13:"image_loading";a:1:{s:9:"attribute";s:4:"lazy";}}s:20:"third_party_settings";a:0:{}s:6:"weight";i:-1;s:6:"region";s:7:"content";}s:10:"field_tags";a:6:{s:4:"type";s:22:"entity_reference_label";s:5:"label";s:5:"above";s:8:"settings";a:1:{s:4:"link";b:1;}s:20:"third_party_settings";a:0:{}s:6:"weight";i:10;s:6:"region";s:7:"content";}s:5:"links";a:4:{s:8:"settings";a:0:{}s:20:"third_party_settings";a:0:{}s:6:"weight";i:100;s:6:"region";s:7:"content";}}s:6:"hidden";a:1:{s:10:"field_tags";b:1;}}',
// +  'data' => 'a:10:{s:4:"uuid";s:36:"684de49b-f163-4427-9700-af1a48323f3e";s:8:"langcode";s:2:"en";s:6:"status";b:1;s:12:"dependencies";a:2:{s:6:"config";a:8:{i:0;s:48:"core.entity_view_display.comment.comment.default";i:1;s:29:"field.field.node.article.body";i:2;s:32:"field.field.node.article.comment";i:3;s:36:"field.field.node.article.field_image";i:4;s:40:"field.field.node.article.field_meta_tags";i:5;s:35:"field.field.node.article.field_tags";i:6;s:17:"image.style.large";i:7;s:17:"node.type.article";}s:6:"module";a:5:{i:0;s:7:"comment";i:1;s:5:"image";i:2;s:7:"metatag";i:3;s:4:"text";i:4;s:4:"user";}}s:2:"id";s:20:"node.article.default";s:16:"targetEntityType";s:4:"node";s:6:"bundle";s:7:"article";s:4:"mode";s:7:"default";s:7:"content";a:6:{s:4:"body";a:6:{s:4:"type";s:12:"text_default";s:5:"label";s:6:"hidden";s:8:"settings";a:0:{}s:20:"third_party_settings";a:0:{}s:6:"weight";i:0;s:6:"region";s:7:"content";}s:7:"comment";a:6:{s:4:"type";s:15:"comment_default";s:5:"label";s:5:"above";s:8:"settings";a:2:{s:9:"view_mode";s:7:"default";s:8:"pager_id";i:0;}s:20:"third_party_settings";a:0:{}s:6:"weight";i:20;s:6:"region";s:7:"content";}s:11:"field_image";a:6:{s:4:"type";s:5:"image";s:5:"label";s:6:"hidden";s:8:"settings";a:3:{s:10:"image_link";s:0:"";s:11:"image_style";s:5:"large";s:13:"image_loading";a:1:{s:9:"attribute";s:4:"lazy";}}s:20:"third_party_settings";a:0:{}s:6:"weight";i:-1;s:6:"region";s:7:"content";}s:15:"field_meta_tags";a:6:{s:4:"type";s:23:"metatag_empty_formatter";s:5:"label";s:5:"above";s:8:"settings";a:0:{}s:20:"third_party_settings";a:0:{}s:6:"weight";i:101;s:6:"region";s:7:"content";}s:10:"field_tags";a:6:{s:4:"type";s:22:"entity_reference_label";s:5:"label";s:5:"above";s:8:"settings";a:1:{s:4:"link";b:1;}s:20:"third_party_settings";a:0:{}s:6:"weight";i:10;s:6:"region";s:7:"content";}s:5:"links";a:4:{s:8:"settings";a:0:{}s:20:"third_party_settings";a:0:{}s:6:"weight";i:100;s:6:"region";s:7:"content";}}s:6:"hidden";a:1:{s:10:"field_tags";b:1;}}',
//  ])

$key_value = $connection->select('key_value')
  ->fields('key_value', ['value'])
  ->condition('collection', 'entity.definitions.bundle_field_map')
  ->condition('name', 'node')
  ->execute()
  ->fetchField();
$key_value = unserialize($key_value, ['allowed_classes' => []]);
$key_value['field_meta_tags'] = [
  'type' => 'metatag',
  'bundles' => [
    'article' => 'article',
  ],
];
$connection->update('key_value')
  ->fields(['value' => serialize($key_value)])
  ->condition('collection', 'entity.definitions.bundle_field_map')
  ->condition('name', 'node')
  ->execute();

// This is not a good way of doing it, but there may not be many good ways of
// doing it.
$connection->update('key_value')
  ->fields([
    'value' => 'a:23:{s:3:"nid";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:7:"integer";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:3:{s:4:"type";s:3:"int";s:8:"unsigned";b:1;s:4:"size";s:6:"normal";}}s:11:"unique keys";a:0:{}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:2;s:13:" * definition";a:2:{s:4:"type";s:18:"field_item:integer";s:8:"settings";a:6:{s:8:"unsigned";b:1;s:4:"size";s:6:"normal";s:3:"min";s:0:"";s:3:"max";s:0:"";s:6:"prefix";s:0:"";s:6:"suffix";s:0:"";}}}s:13:" * definition";a:7:{s:5:"label";s:7:"Node ID";s:11:"description";s:12:"The node ID.";s:9:"read-only";b:1;s:8:"provider";s:4:"node";s:10:"field_name";s:3:"nid";s:11:"entity_type";s:4:"node";s:6:"bundle";N;}}s:4:"uuid";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:4:"uuid";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:3:{s:4:"type";s:13:"varchar_ascii";s:6:"length";i:128;s:6:"binary";b:0;}}s:11:"unique keys";a:1:{s:5:"value";a:1:{i:0;s:5:"value";}}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:33;s:13:" * definition";a:2:{s:4:"type";s:15:"field_item:uuid";s:8:"settings";a:3:{s:10:"max_length";i:128;s:8:"is_ascii";b:1;s:14:"case_sensitive";b:0;}}}s:13:" * definition";a:7:{s:5:"label";s:4:"UUID";s:11:"description";s:14:"The node UUID.";s:9:"read-only";b:1;s:8:"provider";s:4:"node";s:10:"field_name";s:4:"uuid";s:11:"entity_type";s:4:"node";s:6:"bundle";N;}}s:3:"vid";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:7:"integer";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:3:{s:4:"type";s:3:"int";s:8:"unsigned";b:1;s:4:"size";s:6:"normal";}}s:11:"unique keys";a:0:{}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:63;s:13:" * definition";a:2:{s:4:"type";s:18:"field_item:integer";s:8:"settings";a:6:{s:8:"unsigned";b:1;s:4:"size";s:6:"normal";s:3:"min";s:0:"";s:3:"max";s:0:"";s:6:"prefix";s:0:"";s:6:"suffix";s:0:"";}}}s:13:" * definition";a:7:{s:5:"label";s:11:"Revision ID";s:11:"description";s:21:"The node revision ID.";s:9:"read-only";b:1;s:8:"provider";s:4:"node";s:10:"field_name";s:3:"vid";s:11:"entity_type";s:4:"node";s:6:"bundle";N;}}s:4:"type";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:16:"entity_reference";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:9:"target_id";a:3:{s:11:"description";s:28:"The ID of the target entity.";s:4:"type";s:13:"varchar_ascii";s:6:"length";i:32;}}s:7:"indexes";a:1:{s:9:"target_id";a:1:{i:0;s:9:"target_id";}}s:11:"unique keys";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:94;s:13:" * definition";a:2:{s:4:"type";s:27:"field_item:entity_reference";s:8:"settings";a:3:{s:11:"target_type";s:9:"node_type";s:7:"handler";s:12:"default:node";s:16:"handler_settings";a:0:{}}}}s:13:" * definition";a:7:{s:5:"label";s:4:"Type";s:11:"description";s:14:"The node type.";s:9:"read-only";b:1;s:8:"provider";s:4:"node";s:10:"field_name";s:4:"type";s:11:"entity_type";s:4:"node";s:6:"bundle";N;}}s:8:"langcode";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:8:"language";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:2:{s:4:"type";s:13:"varchar_ascii";s:6:"length";i:12;}}s:11:"unique keys";a:0:{}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:124;s:13:" * definition";a:2:{s:4:"type";s:19:"field_item:language";s:8:"settings";a:0:{}}}s:13:" * definition";a:10:{s:5:"label";s:8:"Language";s:11:"description";s:23:"The node language code.";s:12:"translatable";b:1;s:12:"revisionable";b:1;s:7:"display";a:2:{s:4:"view";a:1:{s:7:"options";a:1:{s:4:"type";s:6:"hidden";}}s:4:"form";a:1:{s:7:"options";a:2:{s:4:"type";s:15:"language_select";s:6:"weight";i:2;}}}s:8:"provider";s:4:"node";s:10:"field_name";s:8:"langcode";s:11:"entity_type";s:4:"node";s:6:"bundle";N;s:13:"initial_value";N;}}s:5:"title";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:6:"string";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:3:{s:4:"type";s:7:"varchar";s:6:"length";i:255;s:6:"binary";b:0;}}s:11:"unique keys";a:0:{}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:158;s:13:" * definition";a:2:{s:4:"type";s:17:"field_item:string";s:8:"settings";a:3:{s:10:"max_length";i:255;s:8:"is_ascii";b:0;s:14:"case_sensitive";b:0;}}}s:13:" * definition";a:10:{s:5:"label";s:5:"Title";s:8:"required";b:1;s:12:"translatable";b:1;s:12:"revisionable";b:1;s:13:"default_value";a:1:{i:0;a:1:{s:5:"value";s:0:"";}}s:7:"display";a:2:{s:4:"view";a:1:{s:7:"options";a:3:{s:5:"label";s:6:"hidden";s:4:"type";s:6:"string";s:6:"weight";i:-5;}}s:4:"form";a:2:{s:7:"options";a:2:{s:4:"type";s:16:"string_textfield";s:6:"weight";i:-5;}s:12:"configurable";b:1;}}s:8:"provider";s:4:"node";s:10:"field_name";s:5:"title";s:11:"entity_type";s:4:"node";s:6:"bundle";N;}}s:3:"uid";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:16:"entity_reference";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:9:"target_id";a:3:{s:11:"description";s:28:"The ID of the target entity.";s:4:"type";s:3:"int";s:8:"unsigned";b:1;}}s:7:"indexes";a:1:{s:9:"target_id";a:1:{i:0;s:9:"target_id";}}s:11:"unique keys";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:201;s:13:" * definition";a:2:{s:4:"type";s:27:"field_item:entity_reference";s:8:"settings";a:3:{s:11:"target_type";s:4:"user";s:7:"handler";s:7:"default";s:16:"handler_settings";a:0:{}}}}s:13:" * definition";a:11:{s:5:"label";s:11:"Authored by";s:11:"description";s:35:"The username of the content author.";s:12:"revisionable";b:1;s:22:"default_value_callback";s:41:"Drupal\node\Entity\Node::getCurrentUserId";s:12:"translatable";b:1;s:7:"display";a:2:{s:4:"view";a:1:{s:7:"options";a:3:{s:5:"label";s:6:"hidden";s:4:"type";s:6:"author";s:6:"weight";i:0;}}s:4:"form";a:2:{s:7:"options";a:3:{s:4:"type";s:29:"entity_reference_autocomplete";s:6:"weight";i:5;s:8:"settings";a:3:{s:14:"match_operator";s:8:"CONTAINS";s:4:"size";s:2:"60";s:11:"placeholder";s:0:"";}}s:12:"configurable";b:1;}}s:8:"provider";s:4:"node";s:10:"field_name";s:3:"uid";s:11:"entity_type";s:4:"node";s:6:"bundle";N;s:13:"initial_value";N;}}s:6:"status";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:7:"boolean";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:2:{s:4:"type";s:3:"int";s:4:"size";s:4:"tiny";}}s:11:"unique keys";a:0:{}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:249;s:13:" * definition";a:2:{s:4:"type";s:18:"field_item:boolean";s:8:"settings";a:2:{s:8:"on_label";s:2:"On";s:9:"off_label";s:3:"Off";}}}s:13:" * definition";a:10:{s:5:"label";s:17:"Publishing status";s:11:"description";s:51:"A boolean indicating whether the node is published.";s:12:"revisionable";b:1;s:12:"translatable";b:1;s:13:"default_value";a:1:{i:0;a:1:{s:5:"value";b:1;}}s:8:"provider";s:4:"node";s:10:"field_name";s:6:"status";s:11:"entity_type";s:4:"node";s:6:"bundle";N;s:13:"initial_value";N;}}s:7:"created";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:7:"created";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:1:{s:4:"type";s:3:"int";}}s:11:"unique keys";a:0:{}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:280;s:13:" * definition";a:2:{s:4:"type";s:18:"field_item:created";s:8:"settings";a:0:{}}}s:13:" * definition";a:9:{s:5:"label";s:11:"Authored on";s:11:"description";s:35:"The time that the node was created.";s:12:"revisionable";b:1;s:12:"translatable";b:1;s:7:"display";a:2:{s:4:"view";a:1:{s:7:"options";a:3:{s:5:"label";s:6:"hidden";s:4:"type";s:9:"timestamp";s:6:"weight";i:0;}}s:4:"form";a:2:{s:7:"options";a:2:{s:4:"type";s:18:"datetime_timestamp";s:6:"weight";i:10;}s:12:"configurable";b:1;}}s:8:"provider";s:4:"node";s:10:"field_name";s:7:"created";s:11:"entity_type";s:4:"node";s:6:"bundle";N;}}s:7:"changed";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:7:"changed";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:1:{s:4:"type";s:3:"int";}}s:11:"unique keys";a:0:{}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:315;s:13:" * definition";a:2:{s:4:"type";s:18:"field_item:changed";s:8:"settings";a:0:{}}}s:13:" * definition";a:8:{s:5:"label";s:7:"Changed";s:11:"description";s:39:"The time that the node was last edited.";s:12:"revisionable";b:1;s:12:"translatable";b:1;s:8:"provider";s:4:"node";s:10:"field_name";s:7:"changed";s:11:"entity_type";s:4:"node";s:6:"bundle";N;}}s:7:"promote";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:7:"boolean";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:2:{s:4:"type";s:3:"int";s:4:"size";s:4:"tiny";}}s:11:"unique keys";a:0:{}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:339;s:13:" * definition";a:2:{s:4:"type";s:18:"field_item:boolean";s:8:"settings";a:2:{s:8:"on_label";s:2:"On";s:9:"off_label";s:3:"Off";}}}s:13:" * definition";a:9:{s:5:"label";s:22:"Promoted to front page";s:12:"revisionable";b:1;s:12:"translatable";b:1;s:13:"default_value";a:1:{i:0;a:1:{s:5:"value";b:1;}}s:7:"display";a:1:{s:4:"form";a:2:{s:7:"options";a:3:{s:4:"type";s:16:"boolean_checkbox";s:8:"settings";a:1:{s:13:"display_label";b:1;}s:6:"weight";i:15;}s:12:"configurable";b:1;}}s:8:"provider";s:4:"node";s:10:"field_name";s:7:"promote";s:11:"entity_type";s:4:"node";s:6:"bundle";N;}}s:6:"sticky";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:7:"boolean";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:2:{s:4:"type";s:3:"int";s:4:"size";s:4:"tiny";}}s:11:"unique keys";a:0:{}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:376;s:13:" * definition";a:2:{s:4:"type";s:18:"field_item:boolean";s:8:"settings";a:2:{s:8:"on_label";s:2:"On";s:9:"off_label";s:3:"Off";}}}s:13:" * definition";a:9:{s:5:"label";s:22:"Sticky at top of lists";s:12:"revisionable";b:1;s:12:"translatable";b:1;s:13:"default_value";a:1:{i:0;a:1:{s:5:"value";b:0;}}s:7:"display";a:1:{s:4:"form";a:2:{s:7:"options";a:3:{s:4:"type";s:16:"boolean_checkbox";s:8:"settings";a:1:{s:13:"display_label";b:1;}s:6:"weight";i:16;}s:12:"configurable";b:1;}}s:8:"provider";s:4:"node";s:10:"field_name";s:6:"sticky";s:11:"entity_type";s:4:"node";s:6:"bundle";N;}}s:18:"revision_timestamp";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:7:"created";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:1:{s:4:"type";s:3:"int";}}s:11:"unique keys";a:0:{}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:413;s:13:" * definition";a:2:{s:4:"type";s:18:"field_item:created";s:8:"settings";a:0:{}}}s:13:" * definition";a:8:{s:5:"label";s:18:"Revision timestamp";s:11:"description";s:47:"The time that the current revision was created.";s:9:"queryable";b:0;s:12:"revisionable";b:1;s:8:"provider";s:4:"node";s:10:"field_name";s:18:"revision_timestamp";s:11:"entity_type";s:4:"node";s:6:"bundle";N;}}s:12:"revision_uid";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:16:"entity_reference";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:9:"target_id";a:3:{s:11:"description";s:28:"The ID of the target entity.";s:4:"type";s:3:"int";s:8:"unsigned";b:1;}}s:7:"indexes";a:1:{s:9:"target_id";a:1:{i:0;s:9:"target_id";}}s:11:"unique keys";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:437;s:13:" * definition";a:2:{s:4:"type";s:27:"field_item:entity_reference";s:8:"settings";a:3:{s:11:"target_type";s:4:"user";s:7:"handler";s:12:"default:node";s:16:"handler_settings";a:0:{}}}}s:13:" * definition";a:8:{s:5:"label";s:16:"Revision user ID";s:11:"description";s:50:"The user ID of the author of the current revision.";s:9:"queryable";b:0;s:12:"revisionable";b:1;s:8:"provider";s:4:"node";s:10:"field_name";s:12:"revision_uid";s:11:"entity_type";s:4:"node";s:6:"bundle";N;}}s:12:"revision_log";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:11:"string_long";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:2:{s:4:"type";s:4:"text";s:4:"size";s:3:"big";}}s:11:"unique keys";a:0:{}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:468;s:13:" * definition";a:2:{s:4:"type";s:22:"field_item:string_long";s:8:"settings";a:1:{s:14:"case_sensitive";b:0;}}}s:13:" * definition";a:9:{s:5:"label";s:20:"Revision log message";s:11:"description";s:43:"Briefly describe the changes you have made.";s:12:"revisionable";b:1;s:12:"translatable";b:1;s:7:"display";a:1:{s:4:"form";a:1:{s:7:"options";a:3:{s:4:"type";s:15:"string_textarea";s:6:"weight";i:25;s:8:"settings";a:1:{s:4:"rows";i:4;}}}}s:8:"provider";s:4:"node";s:10:"field_name";s:12:"revision_log";s:11:"entity_type";s:4:"node";s:6:"bundle";N;}}s:29:"revision_translation_affected";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:7:"boolean";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:2:{s:4:"type";s:3:"int";s:4:"size";s:4:"tiny";}}s:11:"unique keys";a:0:{}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:501;s:13:" * definition";a:2:{s:4:"type";s:18:"field_item:boolean";s:8:"settings";a:2:{s:8:"on_label";O:48:"Drupal\Core\StringTranslation\TranslatableMarkup":3:{s:9:" * string";s:2:"On";s:12:" * arguments";a:0:{}s:10:" * options";a:0:{}}s:9:"off_label";O:48:"Drupal\Core\StringTranslation\TranslatableMarkup":3:{s:9:" * string";s:3:"Off";s:12:" * arguments";a:0:{}s:10:" * options";a:0:{}}}}}s:13:" * definition";a:9:{s:5:"label";O:48:"Drupal\Core\StringTranslation\TranslatableMarkup":3:{s:9:" * string";s:29:"Revision translation affected";s:12:" * arguments";a:0:{}s:10:" * options";a:0:{}}s:11:"description";O:48:"Drupal\Core\StringTranslation\TranslatableMarkup":3:{s:9:" * string";s:72:"Indicates if the last edit of a translation belongs to current revision.";s:12:" * arguments";a:0:{}s:10:" * options";a:0:{}}s:9:"read-only";b:1;s:12:"revisionable";b:1;s:12:"translatable";b:1;s:10:"field_name";s:29:"revision_translation_affected";s:11:"entity_type";s:4:"node";s:8:"provider";s:4:"node";s:6:"bundle";N;}}s:16:"default_langcode";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:7:"boolean";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:2:{s:4:"type";s:3:"int";s:4:"size";s:4:"tiny";}}s:11:"unique keys";a:0:{}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:541;s:13:" * definition";a:2:{s:4:"type";s:18:"field_item:boolean";s:8:"settings";a:2:{s:8:"on_label";s:2:"On";s:9:"off_label";s:3:"Off";}}}s:13:" * definition";a:9:{s:5:"label";s:19:"Default translation";s:11:"description";s:58:"A flag indicating whether this is the default translation.";s:12:"translatable";b:1;s:12:"revisionable";b:1;s:13:"default_value";a:1:{i:0;a:1:{s:5:"value";b:1;}}s:8:"provider";s:4:"node";s:10:"field_name";s:16:"default_langcode";s:11:"entity_type";s:4:"node";s:6:"bundle";N;}}s:4:"body";O:38:"Drupal\field\Entity\FieldStorageConfig":30:{s:5:" * id";s:9:"node.body";s:13:" * field_name";s:4:"body";s:14:" * entity_type";s:4:"node";s:7:" * type";s:17:"text_with_summary";s:9:" * module";s:4:"text";s:11:" * settings";a:0:{}s:14:" * cardinality";i:1;s:15:" * translatable";b:1;s:9:" * locked";b:0;s:25:" * persist_with_no_fields";b:1;s:14:"custom_storage";b:0;s:10:" * indexes";a:0:{}s:10:" * deleted";b:0;s:13:" * originalId";s:9:"node.body";s:9:" * status";b:1;s:7:" * uuid";s:36:"ed2b1eb9-b633-4f00-9006-6b70e0ffa5a0";s:11:" * langcode";s:2:"en";s:23:" * third_party_settings";a:0:{}s:8:" * _core";a:0:{}s:14:" * trustedData";b:0;s:15:" * entityTypeId";s:20:"field_storage_config";s:15:" * enforceIsNew";N;s:12:" * typedData";N;s:16:" * cacheContexts";a:0:{}s:12:" * cacheTags";a:0:{}s:14:" * cacheMaxAge";i:-1;s:14:" * _serviceIds";a:0:{}s:18:" * _entityStorages";a:0:{}s:15:" * dependencies";a:1:{s:6:"module";a:2:{i:0;s:4:"node";i:1;s:4:"text";}}s:12:" * isSyncing";b:0;}s:7:"comment";O:38:"Drupal\field\Entity\FieldStorageConfig":30:{s:5:" * id";s:12:"node.comment";s:13:" * field_name";s:7:"comment";s:14:" * entity_type";s:4:"node";s:7:" * type";s:7:"comment";s:9:" * module";s:7:"comment";s:11:" * settings";a:1:{s:12:"comment_type";s:7:"comment";}s:14:" * cardinality";i:1;s:15:" * translatable";b:1;s:9:" * locked";b:0;s:25:" * persist_with_no_fields";b:0;s:14:"custom_storage";b:0;s:10:" * indexes";a:0:{}s:10:" * deleted";b:0;s:13:" * originalId";s:12:"node.comment";s:9:" * status";b:1;s:7:" * uuid";s:36:"34c4c787-8f2a-47d9-a124-3667297f532a";s:11:" * langcode";s:2:"en";s:23:" * third_party_settings";a:0:{}s:8:" * _core";a:0:{}s:14:" * trustedData";b:0;s:15:" * entityTypeId";s:20:"field_storage_config";s:15:" * enforceIsNew";N;s:12:" * typedData";N;s:16:" * cacheContexts";a:0:{}s:12:" * cacheTags";a:0:{}s:14:" * cacheMaxAge";i:-1;s:14:" * _serviceIds";a:0:{}s:18:" * _entityStorages";a:0:{}s:15:" * dependencies";a:1:{s:6:"module";a:2:{i:0;s:7:"comment";i:1;s:4:"node";}}s:12:" * isSyncing";b:0;}s:11:"field_image";O:38:"Drupal\field\Entity\FieldStorageConfig":30:{s:5:" * id";s:16:"node.field_image";s:13:" * field_name";s:11:"field_image";s:14:" * entity_type";s:4:"node";s:7:" * type";s:5:"image";s:9:" * module";s:5:"image";s:11:" * settings";a:5:{s:10:"uri_scheme";s:6:"public";s:13:"default_image";a:5:{s:4:"uuid";N;s:3:"alt";s:0:"";s:5:"title";s:0:"";s:5:"width";N;s:6:"height";N;}s:11:"target_type";s:4:"file";s:13:"display_field";b:0;s:15:"display_default";b:0;}s:14:" * cardinality";i:1;s:15:" * translatable";b:1;s:9:" * locked";b:0;s:25:" * persist_with_no_fields";b:0;s:14:"custom_storage";b:0;s:10:" * indexes";a:1:{s:9:"target_id";a:1:{i:0;s:9:"target_id";}}s:10:" * deleted";b:0;s:13:" * originalId";s:16:"node.field_image";s:9:" * status";b:1;s:7:" * uuid";s:36:"0966a9c5-3884-4c71-ab17-2347f78ac3f4";s:11:" * langcode";s:2:"en";s:23:" * third_party_settings";a:0:{}s:8:" * _core";a:0:{}s:14:" * trustedData";b:0;s:15:" * entityTypeId";s:20:"field_storage_config";s:15:" * enforceIsNew";N;s:12:" * typedData";N;s:16:" * cacheContexts";a:0:{}s:12:" * cacheTags";a:0:{}s:14:" * cacheMaxAge";i:-1;s:14:" * _serviceIds";a:0:{}s:18:" * _entityStorages";a:0:{}s:15:" * dependencies";a:1:{s:6:"module";a:3:{i:0;s:4:"file";i:1;s:5:"image";i:2;s:4:"node";}}s:12:" * isSyncing";b:0;}s:10:"field_tags";O:38:"Drupal\field\Entity\FieldStorageConfig":30:{s:5:" * id";s:15:"node.field_tags";s:13:" * field_name";s:10:"field_tags";s:14:" * entity_type";s:4:"node";s:7:" * type";s:16:"entity_reference";s:9:" * module";s:4:"core";s:11:" * settings";a:1:{s:11:"target_type";s:13:"taxonomy_term";}s:14:" * cardinality";i:-1;s:15:" * translatable";b:1;s:9:" * locked";b:0;s:25:" * persist_with_no_fields";b:0;s:14:"custom_storage";b:0;s:10:" * indexes";a:0:{}s:10:" * deleted";b:0;s:13:" * originalId";s:15:"node.field_tags";s:9:" * status";b:1;s:7:" * uuid";s:36:"e2c2fe69-c14f-4606-8600-e67e248855a9";s:11:" * langcode";s:2:"en";s:23:" * third_party_settings";a:0:{}s:8:" * _core";a:0:{}s:14:" * trustedData";b:0;s:15:" * entityTypeId";s:20:"field_storage_config";s:15:" * enforceIsNew";N;s:12:" * typedData";N;s:16:" * cacheContexts";a:0:{}s:12:" * cacheTags";a:0:{}s:14:" * cacheMaxAge";i:-1;s:14:" * _serviceIds";a:0:{}s:18:" * _entityStorages";a:0:{}s:15:" * dependencies";a:1:{s:6:"module";a:2:{i:0;s:4:"node";i:1;s:8:"taxonomy";}}s:12:" * isSyncing";b:0;}s:16:"revision_default";O:37:"Drupal\Core\Field\BaseFieldDefinition":5:{s:7:" * type";s:7:"boolean";s:9:" * schema";a:4:{s:7:"columns";a:1:{s:5:"value";a:2:{s:4:"type";s:3:"int";s:4:"size";s:4:"tiny";}}s:11:"unique keys";a:0:{}s:7:"indexes";a:0:{}s:12:"foreign keys";a:0:{}}s:10:" * indexes";a:0:{}s:17:" * itemDefinition";O:51:"Drupal\Core\Field\TypedData\FieldItemDataDefinition":2:{s:18:" * fieldDefinition";r:722;s:13:" * definition";a:2:{s:4:"type";s:18:"field_item:boolean";s:8:"settings";a:2:{s:8:"on_label";O:48:"Drupal\Core\StringTranslation\TranslatableMarkup":3:{s:9:" * string";s:2:"On";s:12:" * arguments";a:0:{}s:10:" * options";a:0:{}}s:9:"off_label";O:48:"Drupal\Core\StringTranslation\TranslatableMarkup":3:{s:9:" * string";s:3:"Off";s:12:" * arguments";a:0:{}s:10:" * options";a:0:{}}}}}s:13:" * definition";a:10:{s:5:"label";O:48:"Drupal\Core\StringTranslation\TranslatableMarkup":3:{s:9:" * string";s:16:"Default revision";s:12:" * arguments";a:0:{}s:10:" * options";a:0:{}}s:11:"description";O:48:"Drupal\Core\StringTranslation\TranslatableMarkup":3:{s:9:" * string";s:72:"A flag indicating whether this was a default revision when it was saved.";s:12:" * arguments";a:0:{}s:10:" * options";a:0:{}}s:16:"storage_required";b:1;s:12:"translatable";b:0;s:12:"revisionable";b:1;s:13:"initial_value";a:1:{i:0;a:1:{s:5:"value";b:1;}}s:10:"field_name";s:16:"revision_default";s:11:"entity_type";s:4:"node";s:8:"provider";s:4:"node";s:6:"bundle";N;}}s:15:"field_meta_tags";O:38:"Drupal\field\Entity\FieldStorageConfig":35:{s:5:" * id";s:20:"node.field_meta_tags";s:13:" * field_name";s:15:"field_meta_tags";s:14:" * entity_type";s:4:"node";s:7:" * type";s:7:"metatag";s:9:" * module";s:7:"metatag";s:11:" * settings";a:0:{}s:14:" * cardinality";i:1;s:15:" * translatable";b:1;s:9:" * locked";b:0;s:25:" * persist_with_no_fields";b:0;s:14:"custom_storage";b:0;s:10:" * indexes";a:0:{}s:10:" * deleted";b:0;s:13:" * originalId";s:20:"node.field_meta_tags";s:9:" * status";b:1;s:7:" * uuid";s:36:"6aaab457-3728-4319-afa3-938e753ed342";s:11:" * langcode";s:2:"en";s:23:" * third_party_settings";a:0:{}s:8:" * _core";a:0:{}s:14:" * trustedData";b:0;s:15:" * entityTypeId";s:20:"field_storage_config";s:15:" * enforceIsNew";N;s:12:" * typedData";N;s:16:" * cacheContexts";a:0:{}s:12:" * cacheTags";a:0:{}s:14:" * cacheMaxAge";i:-1;s:14:" * _serviceIds";a:0:{}s:18:" * _entityStorages";a:0:{}s:15:" * dependencies";a:1:{s:6:"module";a:2:{i:0;s:7:"metatag";i:1;s:4:"node";}}s:12:" * isSyncing";b:0;s:18:"cardinality_number";i:1;s:6:"submit";O:48:"Drupal\Core\StringTranslation\TranslatableMarkup":3:{s:9:" * string";s:19:"Save field settings";s:12:" * arguments";a:0:{}s:10:" * options";a:0:{}}s:13:"form_build_id";s:48:"form-LK9HeARuUzcwIVvCAA4jG2MscwGjLAUJ9GLYxuzSo7o";s:10:"form_token";s:43:"eengi9MkLSqT-YFMEKD18fJ6cOvVyS_XRq1He7qhq4s";s:7:"form_id";s:30:"field_storage_config_edit_form";}}',
  ])
  ->condition('collection', 'entity.definitions.installed')
  ->condition('name', 'node.field_storage_definitions')
  ->execute();

$connection->schema()->createTable('node__field_meta_tags', [
  'fields' => [
    'bundle' => [
      'type' => 'varchar_ascii',
      'not null' => TRUE,
      'length' => '128',
      'default' => '',
    ],
    'deleted' => [
      'type' => 'int',
      'not null' => TRUE,
      'size' => 'tiny',
      'default' => '0',
    ],
    'entity_id' => [
      'type' => 'int',
      'not null' => TRUE,
      'size' => 'normal',
      'unsigned' => TRUE,
    ],
    'revision_id' => [
      'type' => 'int',
      'not null' => TRUE,
      'size' => 'normal',
      'unsigned' => TRUE,
    ],
    'langcode' => [
      'type' => 'varchar_ascii',
      'not null' => TRUE,
      'length' => '32',
      'default' => '',
    ],
    'delta' => [
      'type' => 'int',
      'not null' => TRUE,
      'size' => 'normal',
      'unsigned' => TRUE,
    ],
    'field_meta_tags_value' => [
      'type' => 'text',
      'not null' => TRUE,
      'size' => 'big',
    ],
  ],
  'primary key' => [
    'entity_id',
    'deleted',
    'delta',
    'langcode',
  ],
  'indexes' => [
    'bundle' => [
      'bundle',
    ],
    'revision_id' => [
      'revision_id',
    ],
  ],
  'mysql_character_set' => 'utf8mb4',
]);
$connection->schema()->createTable('node_revision__field_meta_tags', [
  'fields' => [
    'bundle' => [
      'type' => 'varchar_ascii',
      'not null' => TRUE,
      'length' => '128',
      'default' => '',
    ],
    'deleted' => [
      'type' => 'int',
      'not null' => TRUE,
      'size' => 'tiny',
      'default' => '0',
    ],
    'entity_id' => [
      'type' => 'int',
      'not null' => TRUE,
      'size' => 'normal',
      'unsigned' => TRUE,
    ],
    'revision_id' => [
      'type' => 'int',
      'not null' => TRUE,
      'size' => 'normal',
      'unsigned' => TRUE,
    ],
    'langcode' => [
      'type' => 'varchar_ascii',
      'not null' => TRUE,
      'length' => '32',
      'default' => '',
    ],
    'delta' => [
      'type' => 'int',
      'not null' => TRUE,
      'size' => 'normal',
      'unsigned' => TRUE,
    ],
    'field_meta_tags_value' => [
      'type' => 'text',
      'not null' => TRUE,
      'size' => 'big',
    ],
  ],
  'primary key' => [
    'entity_id',
    'revision_id',
    'deleted',
    'delta',
    'langcode',
  ],
  'indexes' => [
    'bundle' => [
      'bundle',
    ],
    'revision_id' => [
      'revision_id',
    ],
  ],
  'mysql_character_set' => 'utf8mb4',
]);

// Create a node with values.
// @todo Create a few more.
$connection->insert('comment_entity_statistics')
->fields([
  'entity_id',
  'entity_type',
  'field_name',
  'cid',
  'last_comment_timestamp',
  'last_comment_name',
  'last_comment_uid',
  'comment_count',
])
->values([
  'entity_id' => '1',
  'entity_type' => 'node',
  'field_name' => 'comment',
  'cid' => '0',
  'last_comment_timestamp' => '1669762329',
  'last_comment_name' => NULL,
  'last_comment_uid' => '1',
  'comment_count' => '0',
])
->execute();
$connection->insert('node')
->fields([
  'nid',
  'vid',
  'type',
  'uuid',
  'langcode',
])
->values([
  'nid' => '1',
  'vid' => '1',
  'type' => 'article',
  'uuid' => 'fc2c9449-df04-4d41-beea-5a5b39bf6b89',
  'langcode' => 'en',
])
->execute();
$connection->insert('node__comment')
->fields([
  'bundle',
  'deleted',
  'entity_id',
  'revision_id',
  'langcode',
  'delta',
  'comment_status',
])
->values([
  'bundle' => 'article',
  'deleted' => '0',
  'entity_id' => '1',
  'revision_id' => '1',
  'langcode' => 'en',
  'delta' => '0',
  'comment_status' => '2',
])
->execute();
$connection->insert('node__field_meta_tags')
->fields([
  'bundle',
  'deleted',
  'entity_id',
  'revision_id',
  'langcode',
  'delta',
  'field_meta_tags_value',
])
->values([
  'bundle' => 'article',
  'deleted' => '0',
  'entity_id' => '1',
  'revision_id' => '1',
  'langcode' => 'en',
  'delta' => '0',
  /**
   * Expand this list as new meta tags need to be tested.
   */
  'field_meta_tags_value' => serialize([
    'description' => 'This is a Metatag v1 meta tag.',
    'title' => 'Testing | [site:name]',
    'robots' => 'index, nofollow, noarchive',

    // For #3065441.
    'google_plus_author' => 'GooglePlus Author tag test value for #3065441.',
    'google_plus_description' => 'GooglePlus Description tag test value for #3065441.',
    'google_plus_name' => 'GooglePlus Name tag test value for #3065441.',
    'google_plus_publisher' => 'GooglePlus Publisher tag test value for #3065441.',

    // For #2973351.
    'news_keywords' => 'News Keywords tag test value for #2973351.',
    'standout' => 'Standout tag test value for #2973351.',

    // For #3132065.
    'twitter_cards_data1' => 'Data1 tag test for #3132065.',
    'twitter_cards_data2' => 'Data2 tag test for #3132065.',
    'twitter_cards_dnt' => 'Do Not Track tag test for #3132065.',
    'twitter_cards_gallery_image0' => 'Gallery Image0 tag test for #3132065.',
    'twitter_cards_gallery_image1' => 'Gallery Image1 tag test for #3132065.',
    'twitter_cards_gallery_image2' => 'Gallery Image2 tag test for #3132065.',
    'twitter_cards_gallery_image3' => 'Gallery Image3 tag test for #3132065.',
    'twitter_cards_image_height' => 'Image Height tag test for #3132065.',
    'twitter_cards_image_width' => 'Image Width tag test for #3132065.',
    'twitter_cards_label1' => 'Label1 tag test for #3132065.',
    'twitter_cards_label2' => 'Label2 tag test for #3132065.',
    'twitter_cards_page_url' => 'Page URL tag test for #3132065.',

    // For #3217263.
    'content_language' => 'Content Language tag test for #3217263.',

    // For #3132062.
    'twitter_cards_type' => 'gallery',

    // For #3361816.
    'google_rating' => 'Google Rating tag test for #3361816',
  ]),
])
->execute();
$connection->insert('node_field_data')
->fields([
  'nid',
  'vid',
  'type',
  'title',
  'created',
  'changed',
  'promote',
  'sticky',
  'revision_translation_affected',
  'default_langcode',
  'langcode',
  'status',
  'uid',
])
->values([
  'nid' => '1',
  'vid' => '1',
  'type' => 'article',
  'title' => 'Testing',
  'created' => '1669762311',
  'changed' => '1669762329',
  'promote' => '1',
  'sticky' => '0',
  'revision_translation_affected' => '1',
  'default_langcode' => '1',
  'langcode' => 'en',
  'status' => '1',
  'uid' => '1',
])
->execute();
$connection->insert('node_field_revision')
->fields([
  'nid',
  'vid',
  'title',
  'created',
  'changed',
  'promote',
  'sticky',
  'revision_translation_affected',
  'default_langcode',
  'langcode',
  'status',
  'uid',
])
->values([
  'nid' => '1',
  'vid' => '1',
  'title' => 'Testing',
  'created' => '1669762311',
  'changed' => '1669762329',
  'promote' => '1',
  'sticky' => '0',
  'revision_translation_affected' => '1',
  'default_langcode' => '1',
  'langcode' => 'en',
  'status' => '1',
  'uid' => '1',
])
->execute();
$connection->insert('node_revision__comment')
->fields([
  'bundle',
  'deleted',
  'entity_id',
  'revision_id',
  'langcode',
  'delta',
  'comment_status',
])
->values([
  'bundle' => 'article',
  'deleted' => '0',
  'entity_id' => '1',
  'revision_id' => '1',
  'langcode' => 'en',
  'delta' => '0',
  'comment_status' => '2',
])
->execute();
$connection->insert('node_revision__field_meta_tags')
->fields([
  'bundle',
  'deleted',
  'entity_id',
  'revision_id',
  'langcode',
  'delta',
  'field_meta_tags_value',
])
->values([
  'bundle' => 'article',
  'deleted' => '0',
  'entity_id' => '1',
  'revision_id' => '1',
  'langcode' => 'en',
  'delta' => '0',
  /**
   * Expand this list as new meta tags need to be tested.
   */
  'field_meta_tags_value' => serialize([
    'description' => 'Testing',
    'title' => 'Testing | [site:name]',
    'robots' => 'index, nofollow, noarchive',

    // For #3065441.
    'google_plus_author' => 'GooglePlus Author tag test value for #3065441.',
    'google_plus_description' => 'GooglePlus Description tag test value for #3065441.',
    'google_plus_name' => 'GooglePlus Name tag test value for #3065441.',
    'google_plus_publisher' => 'GooglePlus Publisher tag test value for #3065441.',

    // For #2973351.
    'news_keywords' => 'News Keywords tag test value for #2973351.',
    'standout' => 'Standout tag test value for #2973351.',

    // For #3132065.
    'twitter_cards_data1' => 'Data1 tag test for #3132065.',
    'twitter_cards_data2' => 'Data2 tag test for #3132065.',
    'twitter_cards_dnt' => 'Do Not Track tag test for #3132065.',
    'twitter_cards_gallery_image0' => 'Gallery Image0 tag test for #3132065.',
    'twitter_cards_gallery_image1' => 'Gallery Image1 tag test for #3132065.',
    'twitter_cards_gallery_image2' => 'Gallery Image2 tag test for #3132065.',
    'twitter_cards_gallery_image3' => 'Gallery Image3 tag test for #3132065.',
    'twitter_cards_image_height' => 'Image Height tag test for #3132065.',
    'twitter_cards_image_width' => 'Image Width tag test for #3132065.',
    'twitter_cards_label1' => 'Label1 tag test for #3132065.',
    'twitter_cards_label2' => 'Label2 tag test for #3132065.',
    'twitter_cards_page_url' => 'Page URL tag test for #3132065.',

    // For #3217263.
    'content_language' => 'Content Language tag test for #3217263.',

    // For #3132062.
    'twitter_cards_type' => 'gallery',

    // For #3361816.
    'google_rating' => 'Google Rating tag test for #3361816',
  ]),
])
->execute();
