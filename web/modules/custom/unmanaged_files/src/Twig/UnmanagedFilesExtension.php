use Drupal\image\Entity\ImageStyle;
// Add at top of file if not present.

/**
 * Returns either a file URL or an <img> tag for a random unmanaged file.
 *
 * @param string $format
 *   'url' (default) to return the file URL, or 'img' to return an <img> tag.
 * @param string|null $style
 *   (optional) The image style name to apply when $format is 'img'.
 *
 * @return string|null
 *   URL or HTML string, or NULL if no files found.
 */
public function getRandomFile(string $format = 'url', ?string $style = NULL): ?string {
  $uri = $this->handler->getRandomFile();
  if (!$uri) {
    return NULL;
  }

  // Get the base file URL.
  $url = $this->urlGen->generateAbsoluteString($uri);

  // Apply image style if requested.
  if ($format === 'img' && $style) {
    if ($image_style = ImageStyle::load($style)) {
      $url = $image_style->buildUrl($uri);
    }
  }

  return match ($format) {
    'img' => '<img src="' . $url . '" alt="Random unmanaged file">',
    default => $url,
  };
}
