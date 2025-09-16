<?php

namespace Drupal\unmanaged_files\Service;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;

final class FileHandler {

  public function __construct(
    private FileSystemInterface $fs,
    private StreamWrapperManagerInterface $swm,
  ) {}

  /**
   * Returns a single random file URI under public://segregated_maps,
   * or NULL if none found.
   *
   * @return string|null  e.g., 'public://segregated_maps/africa/algeria.png'
   */
  public function getRandomFile(): ?string {
    $baseUri = 'public://segregated_maps';
    $basePath = $this->fs->realpath($baseUri);
    if (!$basePath || !is_dir($basePath)) {
      return NULL;
    }

    $files = [];
    $iter = new \RecursiveIteratorIterator(
      new \RecursiveDirectoryIterator($basePath, \FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iter as $f) {
      if ($f->isFile()) {
        $abs = $f->getPathname(); // absolute path on disk
        // Convert absolute path back to a public:// URI.
        // $basePath maps to $baseUri.
        $rel = ltrim(substr($abs, strlen($basePath)), DIRECTORY_SEPARATOR);
        $files[] = $baseUri . '/' . str_replace(DIRECTORY_SEPARATOR, '/', $rel);
      }
    }

    if (!$files) {
      return NULL;
    }
    return $files[array_rand($files)];
  }
}
