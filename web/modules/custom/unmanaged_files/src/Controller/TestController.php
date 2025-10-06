<?php

namespace Drupal\unmanaged_files\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\unmanaged_files\Service\FileHandler;
use Drupal\Core\File\FileUrlGeneratorInterface;

/**
 * Test controller to render the module's Twig template.
 */
final class TestController extends ControllerBase {

  /**
   * Constructs the controller.
   *
   * @param \Drupal\unmanaged_files\Service\FileHandler $handler
   *   The unmanaged file handler service.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $urlGen
   *   The file URL generator service.
   */
  public function __construct(
    private FileHandler $handler,
    private FileUrlGeneratorInterface $urlGen,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $c): self {
    /** @var \Drupal\unmanaged_files\Service\FileHandler $handler */
    $handler = $c->get('unmanaged_files.handler');

    /** @var \Drupal\Core\File\FileUrlGeneratorInterface $urlGen */
    $urlGen = $c->get('file_url_generator');

    return new self($handler, $urlGen);
  }

  /**
   * Renders the unmanaged files test page using the module template.
   *
   * @return array
   *   A render array invoking theme hook 'unmanaged_files_test'.
   */
  public function view(): array {
    $uri = $this->handler->getRandomFile();

    if (!$uri) {
      return [
        '#theme' => 'unmanaged_files_test',
        '#message' => $this->t('No files found under public://segregated_maps'),
        '#cache' => ['max-age' => 1],
      ];
    }

    $url = $this->urlGen->generateAbsoluteString($uri);

    return [
      '#theme' => 'unmanaged_files_test',
      '#image_url' => $url,
      '#uri' => $uri,
      '#cache' => ['max-age' => 1],
    ];
  }

}
