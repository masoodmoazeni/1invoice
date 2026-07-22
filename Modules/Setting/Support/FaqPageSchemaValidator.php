<?php

namespace Modules\Setting\Support;

use InvalidArgumentException;

class FaqPageSchemaValidator
{
    public static function validate(?array $schema): void
    {
        if ($schema === null) {
            return;
        }

        if (($schema['@type'] ?? null) !== 'FAQPage') {
            throw new InvalidArgumentException('Schema @type must be "FAQPage".');
        }

        if (
            !isset($schema['mainEntity'])
            || !is_array($schema['mainEntity'])
            || count($schema['mainEntity']) === 0
        ) {
            throw new InvalidArgumentException(
                'Schema must include a non-empty mainEntity array.',
            );
        }

        foreach ($schema['mainEntity'] as $index => $question) {
            if (!is_array($question)) {
                throw new InvalidArgumentException(
                    "mainEntity[{$index}] must be an object.",
                );
            }

            if (($question['@type'] ?? null) !== 'Question') {
                throw new InvalidArgumentException(
                    "mainEntity[{$index}] @type must be \"Question\".",
                );
            }

            if (!is_string($question['name'] ?? null) || trim($question['name']) === '') {
                throw new InvalidArgumentException(
                    "mainEntity[{$index}] must include a non-empty name.",
                );
            }

            $answer = $question['acceptedAnswer'] ?? null;

            if (!is_array($answer)) {
                throw new InvalidArgumentException(
                    "mainEntity[{$index}] must include acceptedAnswer.",
                );
            }

            if (($answer['@type'] ?? null) !== 'Answer') {
                throw new InvalidArgumentException(
                    "mainEntity[{$index}] acceptedAnswer @type must be \"Answer\".",
                );
            }

            if (!is_string($answer['text'] ?? null) || trim($answer['text']) === '') {
                throw new InvalidArgumentException(
                    "mainEntity[{$index}] acceptedAnswer must include non-empty text.",
                );
            }
        }
    }
}
