<?php

namespace Drupal\unmanaged_files\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\unmanaged_files\Service\FileHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Provides a block that displays a random unmanaged file.
 *
 * @Block(
 *   id = "unmanaged_files_block",
 *   admin_label = @Translation("Unmanaged Files Block"),
 * )
 */
final class UnmanagedFilesBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Constructs a new UnmanagedFilesBlock instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the block.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\unmanaged_files\Service\FileHandler $handler
   *   The file handler service for unmanaged files.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $urlGen
   *   The file URL generator service.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private FileHandler $handler,
    private FileUrlGeneratorInterface $urlGen,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * Creates an instance of the UnmanagedFilesBlock.
   *
   * This factory method injects the required services from the container.
   *
   * @param \Symfony\Component\DependencyInjection\ContainerInterface $c
   *   The service container.
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the block.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   *
   * @return static
   *   Returns a new UnmanagedFilesBlock instance.
   */
  public static function create(ContainerInterface $c, array $configuration, $plugin_id, $plugin_definition): self {
    /** @var \Drupal\unmanaged_files\Service\FileHandler $handler */
    $handler = $c->get('unmanaged_files.handler');

    /** @var \Drupal\Core\File\FileUrlGeneratorInterface $urlGen */
    $urlGen = $c->get('file_url_generator');

    return new self(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $handler,
      $urlGen,
    );
  }

  /**
   * Builds the block render array.
   *
   * Uses the file handler to retrieve a random unmanaged file and returns
   * it as an image render array. If no unmanaged files are found, returns
   * a simple message instead.
   *
   * @return array
   *   A render array for the block content.
   */
  public function build(): array {
    $uri = $this->handler->getRandomFile();

    if (!$uri) {
      return [
        '#markup' => '<p>No unmanaged files found.</p>',
      ];
    }

    return [
      '#theme' => 'image',
      '#uri' => $uri,
      '#alt' => $this->t('Random unmanaged file'),
      '#cache' => [
        'max-age' => 1,
      ],
    ];
  }

}
