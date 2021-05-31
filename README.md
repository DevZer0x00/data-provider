# Data Provider

[![Build Status](https://travis-ci.com/DevZer0x00/data-provider.svg?branch=master)](https://travis-ci.com/DevZer0x00/data-provider)
![Packagist Version](https://img.shields.io/packagist/v/devzer0x00/data-provider)
[![codecov](https://codecov.io/gh/DevZer0x00/data-provider/branch/master/graph/badge.svg?token=ZGDSII7OHD)](https://codecov.io/gh/DevZer0x00/data-provider)
![GitHub](https://img.shields.io/github/license/devzer0x00/data-provider)

## Installation

The suggested installation method is via [composer](https://getcomposer.org/):

```bash
$ composer require devzer0x00/data-provider
```

## Basic Usage

```php
use DevZer0x00\DataProvider\ArrayDataProvider;
use DevZer0x00\DataProvider\Paginator;

$originalData = ...;

$provider = new ArrayDataProvider();
$provider->setOriginalData($originalData);
$provider->setPaginator(
    new Paginator([
        'pageSize' => 10,
        'currentPage' => $currentPage
    ])
);

$data = $provider->getData();

```

## Pagination

Вы можете настраивать различные параметры постраничной навигации, такие как:
1. Размер страницы **pageSize**
1. Текущую страницу **currentPage**
1. Общее количество элементов в выборке **totalCount**

Параметры можно установить в констукторе класса **Pagination**, а также при вызове соответствующих методов:
1. setPageSize(int $pageSize)
1. setCurrentPage(int $currentPage)
1. setTotalCount(int $totalCount)

```php
use DevZer0x00\DataProvider\Paginator;

$paginator = new Paginator([
    'pageSize' => 10,
    'currentPage' => 2,
    'totalCount' => 1825
]);

$paginator->setPageSize(25)
    ->setCurrentPage(1)
    ->setTotalCount(4500);
```

## Sort

## Filter