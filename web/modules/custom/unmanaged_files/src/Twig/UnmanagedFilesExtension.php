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

  public function __construct(
    private FileHandler $handler,
    private FileUrlGeneratorInterface $urlGen,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getFunctions(): array {
    return [
      new TwigFunction('random_unmanaged_file', [$this, 'getRandomFile'], ['is_safe' => ['html']]),
    ];
  }

  /**
   * Returns either a file URL or an <img> tag for a random unmanaged file.
   *
   * @param string $format
   *   'url' (default) to return the file URL, or 'img' to return an <img> tag.
   *
   * @return string|null
   *   URL or HTML string, or NULL if no files found.
   */
  public function getRandomFile(string $format = 'url'): ?string {
    $uri = $this->handler->getRandomFile();
    if (!$uri) {
      return NULL;
    }

    $url = $this->urlGen->generateAbsoluteString($uri);

    return match ($format) {
      'img' => '<img src="' . $url . '" alt="Random unmanaged file">',
      default => $url,
    };
  }

}
