<?php

namespace Suite\Amiba\Http\Exports;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExcelExport implements FromCollection {
  public function __construct($data) {
    $this->data = $data;
  }
  public function collection() {
    return $this->data;
  }
}