<?php

namespace FroshViewSnapshots\Services;

use FineDiff;

class Diff
{
    public function __construct()
    {
        require_once implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'lib', 'finediff', 'finediff.php']);
    }

    public function diffPlain(string $from, string $to): FineDiff
    {
        return new FineDiff($from, $to, FineDiff::$wordGranularity);
    }

    public function diffSerialized(string $from, string $to): FineDiff
    {
        return $this->diffPlain($this->prettyPrintSerialized($from), $this->prettyPrintSerialized($to));
    }

    public function diffJson(string $from, string $to): FineDiff
    {
        $fromData = json_decode($from, true);
        $toData = json_decode($to, true);

        if (is_array($fromData) && is_array($toData)) {
            return $this->diffArray($fromData, $toData);
        }

        return $this->diffPlain($from, $to);
    }

    public function diffArray(array $from, array $to): FineDiff
    {
        return $this->diffPlain(
            json_encode($this->sortArrayRecursive($from), JSON_PRETTY_PRINT),
            json_encode($this->sortArrayRecursive($to), JSON_PRETTY_PRINT)
        );
    }

    protected function sortArrayRecursive(array $data): array
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                $value = $this->sortArrayRecursive($value);
            }
        }

        ksort($data);

        return $data;
    }

    protected function prettyPrintSerialized(string $serialized): string
    {
        return print_r(unserialize($serialized), true);
    }
}
