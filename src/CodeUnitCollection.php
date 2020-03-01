<?php declare(strict_types=1);
/*
 * This file is part of sebastian/code-unit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CodeUnit;

final class CodeUnitCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var CodeUnit[]
     */
    private $codeUnits = [];

    /**
     * @param CodeUnit[] $items
     */
    public static function fromArray(array $items): self
    {
        $collection = new self;

        foreach ($items as $item) {
            $collection->add($item);
        }

        return $collection;
    }

    public static function fromList(CodeUnit ...$items): self
    {
        return self::fromArray($items);
    }

    private function __construct()
    {
    }

    /**
     * @return CodeUnit[]
     */
    public function asArray(): array
    {
        return $this->codeUnits;
    }

    public function getIterator(): CodeUnitCollectionIterator
    {
        return new CodeUnitCollectionIterator($this);
    }

    public function count(): int
    {
        return \count($this->codeUnits);
    }

    public function isEmpty(): bool
    {
        return empty($this->codeUnits);
    }

    /**
     * @psalm-return array<string,array<int,int>>
     */
    public function sourceLines(): array
    {
        $result = [];

        foreach ($this as $codeUnit) {
            $sourceFileName = $codeUnit->sourceFileName();

            if (!isset($result[$sourceFileName])) {
                $result[$sourceFileName] = [];
            }

            $result[$sourceFileName] = \array_merge($result[$sourceFileName], $codeUnit->sourceLines());
        }

        foreach (\array_keys($result) as $sourceFileName) {
            $result[$sourceFileName] = \array_unique($result[$sourceFileName]);

            \sort($result[$sourceFileName]);
        }

        \ksort($result);

        return $result;
    }

    private function add(CodeUnit $item): void
    {
        $this->codeUnits[] = $item;
    }
}