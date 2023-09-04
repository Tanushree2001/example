<?php

namespace Drupal\custom_like_button\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\Core\Session\AccountProxyInterface;

/**
 * Provides a 'Related Blogs' block.
 * 
 * @Block(
 *   id = "related_blog_block",
 *   admin_label = @Translation("Related Blogs"),
 *   category = @Translation("Custom")
 * )
 */
class TopThreeLikeBlock extends BlockBase implements ContainerFactoryPluginInterface {

  protected $entityTypeManager;
  protected $currentUser;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager, AccountProxyInterface $currentUser) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->currentUser = $currentUser;
  }

  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('current_user')
    );
  }

  public function build() {
    $authorId = $this->currentUser->id();
    
    // $query = $this->entityTypeManager->getStorage('node')->getQuery();
    // $query->condition('type', 'blogs');
    // $query->condition('uid', $authorId);
    // $query->sort('field_like_counts', 'DESC');
    // $query->range(0,3);
    // $nids = $query->execute();

    // $nodes = $this->entityTypeManager->getStorage('node')->loadMultiple($nids);
    // $output = [];
    // foreach($nodes as $node) {
    //   $output[] = [
    //     '#node' => $node,
    //   ];
    // }
    // return $output;
    
  }

}