<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

class NewsHtmlSanitizer
{
    private const ALLOWED_TAGS = '<p><h2><h3><h4><ul><ol><li><strong><em><blockquote><a><br><figure><figcaption><table><thead><tbody><tr><th><td><caption><hr><img>';

    /**
     * @var array<string, string[]>
     */
    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'target', 'rel'],
        'img' => ['src', 'alt', 'title', 'width', 'height', 'loading'],
        'th' => ['colspan', 'rowspan'],
        'td' => ['colspan', 'rowspan'],
    ];

    public function sanitize(string $html): string
    {
        if (trim($html) === '') {
            return '';
        }

        $cleaned = (string) preg_replace(
            '/<(script|style|iframe|object|embed|form|input|button|textarea|select|option)\b[^>]*>.*?<\/\1>/is',
            '',
            $html
        );

        $cleaned = strip_tags($cleaned, self::ALLOWED_TAGS);

        $document = new DOMDocument();
        $flags = LIBXML_NOERROR | LIBXML_NOWARNING;

        if (defined('LIBXML_HTML_NOIMPLIED')) {
            $flags |= LIBXML_HTML_NOIMPLIED;
        }

        if (defined('LIBXML_HTML_NODEFDTD')) {
            $flags |= LIBXML_HTML_NODEFDTD;
        }

        if (@$document->loadHTML('<?xml encoding="utf-8" ?>' . $cleaned, $flags) === false) {
            return trim($cleaned);
        }

        $this->sanitizeNode($document);

        $body = $document->getElementsByTagName('body')->item(0);

        if ($body instanceof DOMElement) {
            $output = '';

            foreach ($body->childNodes as $child) {
                $output .= $document->saveHTML($child);
            }

            return trim($output);
        }

        $output = $document->saveHTML() ?: '';
        $output = (string) preg_replace('/^<\?xml[^>]+>\s*/', '', $output);

        return trim($output);
    }

    private function sanitizeNode(DOMNode $node): void
    {
        if ($node instanceof DOMElement) {
            $tagName = strtolower($node->tagName);
            $allowedAttributes = self::ALLOWED_ATTRIBUTES[$tagName] ?? [];

            if ($node->hasAttributes()) {
                $attributeNames = [];

                foreach ($node->attributes as $attribute) {
                    $attributeNames[] = $attribute->name;
                }

                foreach ($attributeNames as $attributeName) {
                    $normalized = strtolower($attributeName);

                    if (! in_array($normalized, $allowedAttributes, true)) {
                        $node->removeAttribute($attributeName);
                        continue;
                    }

                    $value = trim((string) $node->getAttribute($attributeName));

                    if ($value === '') {
                        $node->removeAttribute($attributeName);
                        continue;
                    }

                    if (in_array($normalized, ['href', 'src'], true) && ! $this->isSafeUrl($value, $normalized)) {
                        $node->removeAttribute($attributeName);
                        continue;
                    }

                    if ($tagName === 'a' && $normalized === 'target' && ! in_array(strtolower($value), ['_self', '_blank'], true)) {
                        $node->removeAttribute($attributeName);
                    }
                }
            }

            if ($tagName === 'a' && strtolower((string) $node->getAttribute('target')) === '_blank') {
                $node->setAttribute('rel', 'noopener noreferrer');
            }

            if ($tagName === 'img' && $node->hasAttribute('src') && ! $node->hasAttribute('loading')) {
                $node->setAttribute('loading', 'lazy');
            }
        }

        foreach (iterator_to_array($node->childNodes) as $childNode) {
            $this->sanitizeNode($childNode);
        }
    }

    private function isSafeUrl(string $url, string $attribute): bool
    {
        $normalized = preg_replace('/\s+/', '', html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8'));

        if ($normalized === null || $normalized === '') {
            return false;
        }

        $lower = strtolower($normalized);

        if (str_starts_with($lower, 'javascript:') || str_starts_with($lower, 'data:') || str_starts_with($lower, 'vbscript:')) {
            return false;
        }

        if (
            str_starts_with($normalized, '/') ||
            str_starts_with($normalized, './') ||
            str_starts_with($normalized, '../') ||
            str_starts_with($normalized, '#')
        ) {
            return true;
        }

        $parts = parse_url($normalized);

        if (! is_array($parts)) {
            return false;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));

        if ($scheme === '') {
            return true;
        }

        $allowedSchemes = $attribute === 'src'
            ? ['http', 'https']
            : ['http', 'https', 'mailto', 'tel'];

        return in_array($scheme, $allowedSchemes, true);
    }
}
