<?php

namespace Speca\SpecaCore\Export;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportModel implements FromArray, ShouldAutoSize, WithHeadings
{
    use Dispatchable, Exportable, Queueable;

    public function __construct(public array $models, public array $columnsOutput)
    {
        //
    }

    /**
     * Get the inactif account file data as an array.
     *
     * @return array The inactif account file data.
     */
    public function array(): array
    {
        return $this->models;
    }

    /**
     * Get the account file headings.
     *
     * @return string[] The headings.
     */
    public function headings(): array
    {
        return $this->columnsOutput;
    }
}
