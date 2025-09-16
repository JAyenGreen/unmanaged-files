<?php

namespace Drupal\unmanaged_files\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\unmanaged_files\Service\FileHandler;
use Drupal\Core\File\FileUrlGeneratorInterface;

final class TestController extends ControllerBase {

  public function __construct(
    private FileHandler $handler,
    private FileUrlGeneratorInterface $urlGen,
  ) {}

  public static function create(ContainerInterface $c): self {
    return new self(
      $c->get('unmanaged_files.handler'),
      $c->get('file_url_generator'),
    );
  }

  public function view(): array {
    $uri = $this->handler->getRandomFile();

    if (!$uri) {
      return [
        '#markup' => '<p>No files found under <code>public://segregated_maps</code>.</p>',
      ];
    }

    $url = $this->urlGen->generateAbsoluteString($uri);

    return [
      '#type' => 'container',
      '#attributes' => ['class' => ['unmanaged-files-test']],
      'info' => ['#markup' => '<p>Picked: <code>' . $uri . '</code></p>'],
      'img'  => [
        '#type' => 'html_tag',
        '#tag' => 'img',
        '#attributes' => ['src' => $url, 'alt' => 'Random unmanaged file'],
      ],
      '#cache' => [
        // Set the cache life to a minimal duration, for now, so that images change.
        'max-age' => 1,
      ],
    ];
  }
}
