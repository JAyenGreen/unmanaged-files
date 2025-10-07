<?php

namespace Drupal\unmanaged_files\Twig;

use Drupal\unmanaged_files\Service\FileHandler;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Drupal\image\Entity\ImageStyle;
use Drupal\Component\Utility\Html;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Twig extension exposing unmanaged file helpers to templates.
 */
final class UnmanagedFilesExtension extends AbstractExtension {

  /**
   * @param \Drupal\unmanaged_files\Service\FileHandler $handler
   *   Unmanaged file handler service.
   * @param \Drupal\Core\File\FileUrlGeneratorInterface $urlGen
   *   File URL generator.
   */
  public function __construct(
    private FileHandler $handler,
    private FileUrlGeneratorInterface $urlGen,
  ) {}

  /**
   * {@inheritdoc}
   */
  public function getFunctions(): array {
    // Marked is_safe: we return HTML when format = 'img'.
    return [
      new TwigFunction('random_unmanaged_file', [$this, 'randomUnmanagedFile'], [
        'is_safe' => ['html'],
      ]),
    ];
  }

  /**
   * Returns a URL or an <img> tag for a random unmanaged file.
   *
   * Example usage in Twig:
   *   {{ random_unmanaged_file() }}                {# URL #}
   *   {{ random_unmanaged_file('img') }}          {# <img> with original URL #}
   *   {{ random_unmanaged_file('img', 'thumbnail') }}  {# <img> with image style #}
   *
   * @param string $format
   *   'url' (default) to return a URL string; 'img' to return an <img> tag.
   * @param string|null $style
   *   Optional image style machine name (used only when $format = 'img').
   *
   * @return string|null
   *   URL or HTML string, or NULL if no files found.
   */
  public function randomUnmanagedFile(string $format = 'url', ?string $style = NULL): ?string {
    $uri = $this->handler->getRandomFile();
    if (!$uri) {
      return NULL;
    }

    // Start with the absolute file URL.
    $url = $this->urlGen->generateAbsoluteString($uri);

    // If an image style is requested, try to build a styled URL.
    if ($format === 'img' && $style) {
      if ($image_style = ImageStyle::load($style)) {
        // buildUrl() returns a publicly accessible derivative URL.
        $url = $image_style->buildUrl($uri);
      }
    }

    if ($format === 'img') {
      // We declared is_safe, so escape attributes manually.
      $safeUrl = Html::escape($url);
      return '<img src="' . $safeUrl . '" alt="Random unmanaged file">';
    }

    // Default: return the URL string.
    return $url;
  }

}
