<?php

namespace FroshViewSnapshots\Services;

use FineDiff;

class Diff
{
    public function __construct()
    {
        require_once implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'lib', 'finediff', 'finediff.php']);
    }

    /**
     * @param string $from
     * @param string $to
     * @return FineDiff
     */
    public function diffPlain($from, $to)
    {
        return new FineDiff($from, $to, FineDiff::$wordGranularity);
    }

    /**
     * @param string $from
     * @param string $to
     * @return FineDiff
     */
    public function diffSerialized($from, $to)
    {
        return $this->diffPlain($this->prettyPrintSerialized($from), $this->prettyPrintSerialized($to));
    }

    /**
     * @param string $from
     * @param string $to
     * @return FineDiff
     */
    public function diffJson($from, $to)
    {
        $fromData = json_decode($from, true);
        $toData = json_decode($to, true);

        if (is_array($fromData) && is_array($toData)) {
            return $this->diffArray($fromData, $toData);
        }

        return $this->diffPlain($from, $to);
    }

    /**
     * @param array $from
     * @param array $to
     * @return FineDiff
     */
    public function diffArray(array $from, array $to)
    {
        return $this->diffPlain(
            json_encode($this->sortArrayRecursive($from), JSON_PRETTY_PRINT),
            json_encode($this->sortArrayRecursive($to), JSON_PRETTY_PRINT)
        );
    }

    /**
     * @param array $data
     * @return array
     */
    protected function sortArrayRecursive(array $data)
    {
        foreach ($data as &$value) {
            if (is_array($value)) {
                $value = $this->sortArrayRecursive($value);
            }
        }

        ksort($data);

        return $data;
    }

    /**
     * @param string $serialized
     * @return string
     */
    protected function prettyPrintSerialized($serialized)
    {
        return print_r(unserialize($serialized), true);
    }
}
