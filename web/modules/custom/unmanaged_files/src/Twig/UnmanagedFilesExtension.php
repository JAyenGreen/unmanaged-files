<?php

namespace Drupal\unmanaged_files\Twig;

use Drupal\unmanaged_files\Service\FileHandler;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension exposing unmanaged file helpers to templates.
 */
final class UnmanagedFilesExtension extends AbstractExtension {

  /**
   * Constructs the extension.
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
  public function getFunctions(): array {
    return [
      // Returns an absolute URL to a random unmanaged file (or NULL).
      new TwigFunction('random_unmanaged_file', [$this, 'getRandomFileUrl']),
    ];
  }

  /**
   * Returns an absolute URL for a random unmanaged file.
   *
   * @return string|null
   *   A URL string or NULL if none found.
   */
  public function getRandomFileUrl(): ?string {
    $uri = $this->handler->getRandomFile();
    return $uri ? $this->urlGen->generateAbsoluteString($uri) : NULL;
  }

}
