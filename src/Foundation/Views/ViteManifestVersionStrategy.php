<?php

namespace Framewire\Foundation\Views;

use RuntimeException;
use Symfony\Component\Asset\Exception\AssetNotFoundException;
use Symfony\Component\Asset\VersionStrategy\VersionStrategyInterface;

class ViteManifestVersionStrategy implements VersionStrategyInterface
{
    private string $manifestPath;
    private array $manifestData;
    private bool $strictMode;

    /**
     * @param string $manifestPath Absolute path to the manifest file
     * @param bool   $strictMode   Throws an exception for unknown paths
     */
    public function __construct(string $manifestPath, bool $strictMode = false)
    {
        $this->manifestPath = $manifestPath;
        $this->strictMode = $strictMode;
    }

    /**
     * With a manifest, we don't really know or care about what
     * the version is. Instead, this returns the path to the
     * versioned file.
     */
    public function getVersion(string $path): string
    {
        return $this->applyVersion($path);
    }

    public function applyVersion(string $path): string
    {
        return $this->getManifestPath($path) ?: $path;
    }

    private function getManifestPath(string $path): ?string
    {
        if (!isset($this->manifestData)) {
            if (!is_file($this->manifestPath)) {
                throw new RuntimeException(sprintf('Asset manifest file "%s" does not exist. Did you forget to build the assets with npm or yarn?', $this->manifestPath));
            }

            try {
                $this->manifestData = json_decode(file_get_contents($this->manifestPath), true, flags: \JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new RuntimeException(sprintf('Error parsing JSON from asset manifest file "%s": ', $this->manifestPath).$e->getMessage(), previous: $e);
            }
        }

        if (isset($this->manifestData[$path])) {
            return $this->manifestData[$path]['file'];
        }

        if ($this->strictMode) {
            $message = sprintf('Asset "%s" not found in manifest "%s".', $path, $this->manifestPath);
            $alternatives = $this->findAlternatives($path, $this->manifestData);
            if (\count($alternatives) > 0) {
                $message .= sprintf(' Did you mean one of these? "%s".', implode('", "', $alternatives));
            }

            throw new AssetNotFoundException($message, $alternatives);
        }

        return null;
    }

    private function findAlternatives(string $path, array $manifestData): array
    {
        $path = strtolower($path);
        $alternatives = [];

        foreach ($manifestData as $key => $value) {
            $lev = levenshtein($path, strtolower($key));
            if ($lev <= \strlen($path) / 3 || false !== stripos($key, $path)) {
                $alternatives[$key] = isset($alternatives[$key]) ? min($lev, $alternatives[$key]) : $lev;
            }

            $lev = levenshtein($path, strtolower($value));
            if ($lev <= \strlen($path) / 3 || false !== stripos($key, $path)) {
                $alternatives[$key] = isset($alternatives[$key]) ? min($lev, $alternatives[$key]) : $lev;
            }
        }

        asort($alternatives);

        return array_keys($alternatives);
    }
}
