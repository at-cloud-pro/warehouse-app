<?php

declare(strict_types=1);

namespace App\Tests\Behat\Helper;

use Webmozart\Assert\Assert;

trait JsonTraverseTrait
{
    /**
     * @param array<mixed> $subject
     *
     * @return array<mixed>|bool|int|string
     */
    public function traverse(array $subject, StructureTraversePath $path): mixed
    {
        if ($path->isProperty()) {
            return $subject[$path->getByIndex(0)];
        }

        $elements = $path->getElements();

        $currentScope = $subject;
        foreach ($elements as $element) {
            Assert::string($element);

            // foreach element detected
            if ('[]' === $element) {
                next($elements);
                next($elements);

                $nextElement = current($elements);

                // only the filter syntax is allowed after forEach "[]" call
                Assert::string($nextElement);
                Assert::true(str_starts_with($nextElement, '{'));
                Assert::true(str_ends_with($nextElement, '}'));

                $fields = $this->parseFilterFields($nextElement);

                $output = [];
                foreach ($currentScope as $value) {
                    $output[] = $this->formatByFieldList($value, $fields);
                }

                return $output;
            }

            // filter element detected
            if (str_starts_with($element, '{') && str_ends_with($element, '}')) {
                $fields = $this->parseFilterFields($element);
                $currentScope = $this->formatByFieldList($currentScope, $fields);

                continue;
            }

            // list item by index
            if (is_numeric($element)) {
                $element = (int) $element;
            }

            // get value
            Assert::keyExists($currentScope, $element);
            $currentScope = $currentScope[$element];
        }

        return $currentScope;
    }

    /** @return string[] */
    private function parseFilterFields(string $element): array
    {
        $elementWithoutBraces = str_replace(['{', '}'], ['', ''], $element);
        $fields = explode(',', $elementWithoutBraces);

        $trimmedFields = [];
        foreach ($fields as $field) {
            $trimmedFields[] = trim($field);
        }

        return $trimmedFields;
    }

    /**
     * @param array<mixed> $input
     * @param string[]     $fieldList
     *
     * @return array<mixed>
     */
    private function formatByFieldList(array $input, array $fieldList = []): array
    {
        // treat empty field list as none
        if ([] === $fieldList) {
            return [];
        }

        // treat asterisk as all
        if ($fieldList === ['*']) {
            return $input;
        }

        $output = [];

        foreach ($fieldList as $field) {
            Assert::keyExists($input, $field);
            $output[$field] = $input[$field];
        }

        return $output;
    }
}
