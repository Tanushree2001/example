<?php
namespace Drupal\custom_signin\Plugin\rest\resource;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
/**
 * Provides a Content 
 * 
 * @RestResource(
 *   id = "content",
 *   label = @Translation("Blog Contents"),
 *   uri_paths = {
 *     "canonical" = "/get/blogs"
 *   }
 * )
 */
class GetContent extends ResourceBase {

  // /**
  //  * The entity type manager.
  //  * 
  //  * @var \Drupal\Core\Entity\EntityTypeManagerInterface
  //  */
  // protected $entityTypeManager;

  // /**
  //  * Constructs a new GetContent object.
  //  * 
  //  * @param array $configuration
  //  *   A configuration array containing information about the plugin instance.
  //  * @param string $plugin_id
  //  *   The plugin_id for the plugin instance.
  //  * @param mixed $plugin_definition 
  //  *   The plugin implementation definition.
  //  * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
  //  *   The entity type manager.
  //  */
  // public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
  //   parent::__construct($configuration, $plugin_id, $plugin_definition);
  //   $this->entityTypeManager = $entity_type_manager;
  // }

  // /**
  //  * {@inheritdoc}
  //  */
  // public static function create(ContainerInterface $container) {
  //   return new static(
  //     // $configuration,
  //     // $plugin_id,
  //     // $plugin_definition,
  //     // $container->get('config.factory')->get('custom_signin.settings'),
  //     [],
  //     'content',
  //     [],
  //     $container->get('entity_type.manager')
  //   );
  // }

  /** 
   * Responds to entity GET requests.
   * 
   * @return \Drupal\rest\ResouceResponse
   */
  public function get() {
    $nids = \Drupal::entityQuery('node')
      ->condition('type', 'blogs')
      ->accessCheck(FALSE)
      ->execute();
    // $nids = $this->entityTypeManager->getStorage('node')
    //   ->getQuery()
    //   ->condition('type', 'blogs')
    //   ->execute();
    $nodes = \Drupal\node\Entity\Node::loadMultiple($nids);
    $output = $this->processNodes($nodes);

    // if (isset($output['author'])) {
    //   $query->condition('s.stream', $parameters['stream']);
    // }
    return new ResourceResponse($output);
  }

  /**
   * Get Content
   */
  private function processNodes(array $nodes){
    $output = [];
    foreach ($nodes as $key=> $node){
      $output[$key]['title'] = $node->getTitle();
      $output[$key]['body'] = $node->get('body')->value; 
      $output[$key]['published_date'] = $node->get('field_published_date')->value;
      $output[$key]['author'] = $node->getOwner()->getDisplayName();
      $output[$key]['tag'] = $node->get('field_blog_tags')->entity->getName();
    }
    return $output;
  }
}