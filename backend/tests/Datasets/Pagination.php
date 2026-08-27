<?php
declare(strict_types=1);

dataset('pagination', function () {
    return [
        'first page, 10 per page' => ['itemsCount' => 30, 'page' => 1, 'size' => 10, 'expectedSize' => 10],
        'first page, 20 per page' => ['itemsCount' => 30, 'page' => 1, 'size' => 20, 'expectedSize' => 20],
        'second page, 10 per page' => ['itemsCount' => 30, 'page' => 2, 'size' => 10, 'expectedSize' => 10],
        'second page, partial' => ['itemsCount' => 30, 'page' => 2, 'size' => 20, 'expectedSize' => 10],
        'page out of bounds, none' => ['itemsCount' => 30, 'page' => 4, 'size' => 10, 'expectedSize' => 0],
        'first page, more than total, all per page' => ['itemsCount' => 30, 'page' => 1, 'size' => 40, 'expectedSize' => 30],
    ];
});
