<?php

declare(strict_types=1);

namespace App;

use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use SplFileObject;

class XlsToArrayConverter
{
    const NUMBER_OF_SHEETS_WE_NEED = 4;

    /**
     * @var string
     */
    private $pathname;

    /**
     * @var \PhpOffice\PhpSpreadsheet\Reader\Xls
     */
    private $xls_reader;

    /**
     * @var array|null
     */
    private $images;

    private $placeholder_image = 'https://i.ibb.co/9tpYXHz/fish-placeholder.jpg';

    public function __construct(string $pathname, Xls $xls_reader)
    {
        $this->pathname = $pathname;
        $this->xls_reader = $xls_reader;
        $this->images = $this->getImagesFromCSV();
    }

    /**
     * @return \App\ConversionResult
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function convert(): ConversionResult
    {
        $sheets = $this->xls_reader->load($this->pathname);

        $categories = $this->getArrayFromSheet($sheets);

        $price_list = $this->convertToPriceList($categories[ConversionResult::PRICE_LIST]);
        $equipment = $this->convertToEquipment($categories[ConversionResult::EQUIPMENT]);

        return new ConversionResult($price_list, $equipment, [], []);
    }

    private function getImagesFromCSV(): ?array
    {
        $file_path = storage_path('app/csv/images.csv');

        if (!file_exists($file_path)) {
            return null;
        }

        $file = new SplFileObject($file_path);

        if (is_null($file)) {
            return null;
        }

        $result = [];

        while (!$file->eof()) {
            $csv = $file->fgetcsv();

            if (count($csv) !== 2) {
                continue;
            }

            $result[mb_strtolower(current($csv))] = last($csv);
        }

        return $result;
    }

    /**
     * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $sheets
     *
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    private function getArrayFromSheet(Spreadsheet $sheets): array
    {
        $categories = [
            ConversionResult::PRICE_LIST,
            ConversionResult::EQUIPMENT,
            ConversionResult::FEED,
            ConversionResult::CHEMISTRY,
        ];

        $result = [];

        for ($sheet_index = 0; $sheet_index < self::NUMBER_OF_SHEETS_WE_NEED; $sheet_index++) {
            $sheet = $sheets->getSheet($sheet_index);
            $index = 0;

            foreach ($sheet->getColumnIterator() as $column) {
                foreach ($column->getCellIterator() as $cell) {
                    $category = $categories[$sheet_index];
                    $result[$category][$index][] = $cell->getValue();
                }

                $index++;
            }
        }

        return $result;
    }

    /**
     * @param array $price_list
     *
     * @return array[]
     */
    private function convertToPriceList(array $price_list): array
    {
        $result = [];
        $title = '';

        for ($i = 3; $i < count($price_list[0]); $i++) {
            $columns = [
                'number' => $price_list[0][$i],
                'name' => $price_list[1][$i],
                'size' => $price_list[2][$i],
                'price' => $price_list[3][$i],
                'comment' => $price_list[4][$i] ?? '',
                'order' => $price_list[5][$i] ?? '',
                'sum' => $price_list[6][$i] ?? '0.00',
            ];

            $not_nulls = array_filter($columns, function ($item) {
                return !is_null($item) && $item !== '' && $item !== '0.00';
            });

            if (empty($not_nulls)) {
                continue;
            }

            if (count($not_nulls) === 1) {
                if (is_object(current($not_nulls))) {
                    continue;
                }

                $title = current($not_nulls);
                continue;
            }

            $image = $this->getImageFrom($columns['name']);

            $result[$title][] = array_merge($columns, compact('image'));
        }

        return $result;
    }

    /**
     * @param array[] $equip
     *
     * @return array[]
     */
    private function convertToEquipment(array $equip): array
    {
        $result = [];
        $title = '';

        for ($i = 3; $i < count($equip[0]); $i++) {
            $columns = [
                'number' => $equip[0][$i],
                'name' => $equip[1][$i],
                'size' => $equip[2][$i],
                'price' => $equip[3][$i],
                'comment' => $equip[4][$i] ?? '',
                'order' => $equip[5][$i] ?? '',
                'sum' => $equip[6][$i] ?? '0.00',
            ];

            $not_nulls = array_filter($columns, function ($item) {
                return !is_null($item) && $item !== '' && $item !== '0.00';
            });

            if (empty($not_nulls)) {
                continue;
            }

            if (count($not_nulls) === 1) {
                if (is_object(current($not_nulls))) {
                    continue;
                }

                $title = current($not_nulls);
                continue;
            }

            $image = $this->getImageFrom($columns['name']);

            $result[$title][] = array_merge($columns, compact('image'));
        }

        return $result;
    }

    private function getImageFrom(?string $name): ?string
    {
        $id = mb_strtolower(preg_replace('!\s+!', ' ', trim($name ?? '')));
        return $this->images[$id] ?? $this->placeholder_image;
    }
}