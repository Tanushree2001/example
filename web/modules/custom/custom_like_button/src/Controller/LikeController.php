<?php

namespace Drupal\custom_like_button\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * class LikeController
 * 
 * @package Drupal\custom_like_button\Controller
 */
class LikeController extends ControllerBase {
  /**
   * Increase the like count of a node.
   * 
   * @param \Drupal\node\NodeInterface $node
   *   The node to like.
   * 
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response containing the number of likes. 
   */
  public function like(NodeInterface $node)
  {
    $likes = $node->get('field_like_counts')->value;
    $node->set('field_like_counts', ++$likes);
    $node->save();

    return new JsonResponse(['likes' => $likes]);
  }
}