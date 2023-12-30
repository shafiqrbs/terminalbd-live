<?php
/**
 * @Author: Roni Kumar Saha
 * Date: 2/12/16
 * Time: 3:43 PM
 */

namespace Appstore\Bundle\InventoryBundle\Importer;


use Liuggio\ExcelBundle\Factory;

class ProductDataReader
{
    /**
     * @var Factory
     */
    private $excelFactory;

    public function __construct(Factory $excelFactory) {
        $this->excelFactory = $excelFactory;
    }

    public function getData($file) {

        $objPHPExcel = $this->excelFactory->createPHPExcelObject($file);

        return $this->getNamedArray($objPHPExcel);
    }

    /**     * @param \PHPExcel $objPHPExcel
     *
     * @return array
     * @throws \PHPExcel_Exception
     * @internal param $file
     *
     */
    protected function getNamedArray(\PHPExcel $objPHPExcel)
    {
        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);
        $highestRow = $objWorksheet->getHighestRow();
        $highestColumn = $objWorksheet->getHighestColumn();

        $headingsArray = $objWorksheet->rangeToArray('A1:' . $highestColumn . '1', NULL, TRUE, TRUE, TRUE);
        $headingsArray = $headingsArray[1];

        $namedDataArray = array();

        for ($row = 2; $row <= $highestRow; ++$row) {
            $dataRow = $objWorksheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, TRUE, TRUE);

            if ((isset($dataRow[$row]['A'])) && ($dataRow[$row]['A'] > '')) {

                $dataArray = array();

                foreach ($headingsArray as $columnKey => $columnHeading) {
                    $dataArray[$columnHeading] = $dataRow[$row][$columnKey];
                }

                $key = $dataArray['ProductName'];

                $namedDataArray[$key] = $dataArray;

            }
        }

        return $namedDataArray;
    }
}