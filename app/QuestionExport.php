<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;



class QuestionExport implements FromCollection
{
    public function __construct(array $data)
    {
        $this->data = $data;
    }
    public function collection()
    {
        // dd($this->data);
        return new collection($this->data);
    }
}
  

