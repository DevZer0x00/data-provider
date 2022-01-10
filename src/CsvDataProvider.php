<?php

declare(strict_types=1);

namespace DevZer0x00\DataProvider;

use DevZer0x00\DataProvider\Exception\ColumnCountException;
use DevZer0x00\DataProvider\Exception\ReadStreamErrorException;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CsvDataProvider extends DataProviderAbstract
{
    private $sourceStream;

    private string $colDelimiter;

    private array $encodings;

    private function parseData()
    {
        $content = trim(stream_get_contents($this->sourceStream));

        if ($content === false) {
            throw new ReadStreamErrorException('Failed to get data from stream');
        }

        $encodedContent = mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, $this->encodings, true));
        $encodedContent = str_replace(["\r\n", "\r", "\n"], "\n", $encodedContent);

        unset($content);

        $lines = explode("\n", $encodedContent);
        $lines = array_filter($lines);
        $lines = array_map('trim', $lines);

        $titles = str_getcsv($lines[0], $this->colDelimiter);
        $columnCount = count($titles);
        unset($lines[0]);
        $lines = array_values($lines);

        $data = [];

        foreach ($lines as $key => $line) {
            $lineArr = str_getcsv($line, $this->colDelimiter);

            if (count($lineArr) < $columnCount) {
                throw new ColumnCountException('Wrong number of filled columns');
            }

            foreach ($lineArr as $index => $columnValue) {
                $data[$key][trim($titles[$index])] = trim($columnValue);
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

    public function setEncodings(array $encodings): self
    {
        $this->refresh();

        $this->encodings = $encodings;

        return $this;
    }

    protected function configureOptions(OptionsResolver $resolver): OptionsResolver
    {
        return parent::configureOptions($resolver)
            ->setDefault('colDelimiter', ';')
            ->setDefault('encodings', ['cp1251', 'UTF-8'])
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

        $arrayDataProvider = new ArrayDataProvider([
            'originalData' => $this->parseData(),
        ]);

        $this->data = $arrayDataProvider->getData();

        return $this;
    }
}
