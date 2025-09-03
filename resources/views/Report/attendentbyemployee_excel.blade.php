
<?php
$i = 1;

$sheet->setCellValueByColumnAndRow( 1, $i, 'Attendance Report' );

$i++;
$i++; // Start with the second line

$titles = array(
    'ETF NO',
    'Name',
    'Department',
    'Date',
    'Check In Time',
    'Check Out Time',
    'Work Hours',
    'Location'
);

foreach ($titles as $key => $value) {
    $sheet->setCellValueByColumnAndRow($key + 1, $i, $value);
}

foreach ($titles as $key => $value) {
    $sheet->setCellValueByColumnAndRow($key + 1, $i, $value);
}

$row = $sheet->getRowIterator($i)->current();
$cellIterator = $row->getCellIterator();
$cellIterator->setIterateOnlyExistingCells(false);
foreach ($cellIterator as $cell) {
    $cell->getStyle()->getFont()->setBold(true);
}

$i++; // Start with the second line

    //foreach $data_arr
    foreach ($data_arr as $key => $value) {
        $sheet->setCellValueByColumnAndRow( 1, $i, $value['etf_no'] );
        $sheet->setCellValueByColumnAndRow( 2, $i, $value['emp_name_with_initial'] );
        $sheet->setCellValueByColumnAndRow( 3, $i, $value['dept_name'] );
        $sheet->setCellValueByColumnAndRow( 4, $i, $value['date'] );
        $sheet->setCellValueByColumnAndRow( 5, $i, $value['timestamp'] );
        $sheet->setCellValueByColumnAndRow( 6, $i, $value['lasttimestamp'] );
        $sheet->setCellValueByColumnAndRow( 7, $i, $value['workhours'] );
        $sheet->setCellValueByColumnAndRow( 8, $i, $value['location'] );
        $i++;
    }

//$row = $sheet->getRowIterator($i)->current();
//$cellIterator = $row->getCellIterator();
//$cellIterator->setIterateOnlyExistingCells(false);
//foreach ($cellIterator as $cell) {
//    $cell->getStyle()->getFont()->setBold(true);
//}
//
//foreach ($sheet->getColumnIterator() as $column) {
//    $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
//}
//
//$hcol = $sheet->getHighestColumn();
//$hc = $hcol.'1';
//$sheet->mergeCells("A1:$hc");
//$sheet->getStyle("A1:$hc")->getAlignment()->setHorizontal('left');
//$sheet->getStyle("A1:$hc")->getFont()->setSize(14);
//$sheet->getStyle("A1:$hc")->getFont()->setBold(true);


