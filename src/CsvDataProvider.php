<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider;

use DevZer0x00\DataProvider\Exception\ColumnCountException;
use DevZer0x00\DataProvider\Exception\FailReadStreamException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CsvDataProvider extends DataProviderAbstract
{
    private $sourceStream;

    private string $colDelimiter;

    private function parseData()
    {
        $content = trim(stream_get_contents($this->sourceStream));

        if (!$content) {
            throw new FailReadStreamException('Failed to get data from stream');
        }

        $encodings = ['cp1251', 'UTF-8'];

        $encodedContent = mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, $encodings, true));
        $encodedContent = str_replace(["\r\n", "\r", "\n"], "\n", $encodedContent);

        unset($content);

        $lines = explode("\n", trim($encodedContent));
        $lines = array_filter($lines);
        $lines = array_map('trim', $lines);

        $title = str_getcsv($lines[0], $this->colDelimiter);
        $columnCount = count($title);
        unset($lines[0]);
        $lines = array_values($lines);

        $data = [];
        foreach ($lines as $key => $line) {
            $lineArr = str_getcsv($line, $this->colDelimiter);
            if (count($lineArr) < $columnCount) {
                throw new ColumnCountException('Wrong number of filled columns');
            }
            foreach ($lineArr as $index => $columnValue) {
                $data[$key][trim($title[$index])] = trim($columnValue);
            }
            unset($lines[$key]);
        }

        return $data;
    }

    public function setSourceStream($sourceStream): self
    {
        $this->refresh();

        $this->sourceStream = $sourceStream;

        return $this;
    }

    public function setColDelimiter(string $colDelimiter): self
    {
        $this->refresh();

        $this->colDelimiter = $colDelimiter;

        return $this;
    }

    protected function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        return parent::configureOptions($resolver)
            ->setDefault('colDelimiter', ';')
            ->setDefined(['sourceStream', 'sortCallback'])
            ->setAllowedTypes('sourceStream', 'resource')
            ->setAllowedTypes('sortCallback', ['null', 'callable']);
    }

    protected function initializeData(): self
    {
        if ($this->initialized) {
            return $this;
        }

        $this->initialized = true;

        $data = $this->parseData();

        $arrayDataProvider = new ArrayDataProvider([
            'originalData' => $data,
        ]);

        $this->data = $arrayDataProvider->getData();

        return $this;
    }
}
