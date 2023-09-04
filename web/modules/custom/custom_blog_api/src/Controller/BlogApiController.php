<?php
namespace Drupal\custom_blog_api\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Drupal\node\Entity\Node;

class BlogApiController extends ControllerBase {
  public function listBlogs(Request $request) {
    $query = \Drupal::entityQuery('node')
      ->condition('type', 'blogs')
      ->accessCheck(FALSE);
     
    $query_parameters = $request->query->all();
    if (!empty($query_parameters['tag'])) {
      $tag = $query_parameters['tag'];
      
      \Drupal::logger('custom_blog_api')->notice('Tag: ' .$tag);
      $query->condition('field_tags', $tag);
    }
    $nids = $query->execute();
    
    $nodes = Node::loadMultiple($nids);
    $data = [];

    foreach ($nodes as $node) {
      $data[] = [
        'title' => $node->getTitle(),
        'body' => $node->get('body')->value,
        'published_date' => $node->getCreatedTime(),
        'author' => $node->getOwner()->getDisplayName(),
        'tag' => $node->field_blog_tags->entity->getName(),
      ];
    }
    return new JsonResponse($data);
  }
}