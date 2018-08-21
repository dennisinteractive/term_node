<?php

namespace Drupal\term_node;

use Drupal\Core\Entity\EntityMalformedException;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\HttpFoundation\Request;

class OutboundResolver implements ResolverInterface {

  /**
   * @inheritDoc
   */
  public function getPath(Request $request, $path, $nid) {
    // Get the tid of a referencing term.
    if ($tid = $this->getReferencedBy($nid)) {
      try {
        if ($term = Term::load($tid)) {
          return $term->toUrl()->toString();
        }
      } catch (EntityMalformedException $e) {
        // Just return the original path on error.
      }
    }

    return $path;
  }

  /**
   * The tid of the term referencing the node.
   */
  public function getReferencedBy($nid) {
    $query = \Drupal::entityQuery('taxonomy_term')
      ->condition('field_term_node', $nid)
    ;
    $tids = $query->execute();
    if (count($tids) > 0) {
      return reset($tids);
    }

    return FALSE;
  }
}
