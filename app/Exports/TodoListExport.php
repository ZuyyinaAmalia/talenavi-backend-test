<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

// Export Excel untuk TodoList 
class TodoListExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    protected $data;

    // Inisialisasi data yang akan diekspor
    public function __construct($data) {
        $this->data = $data;
    }
    
    // Mengembalikan koleksi data untuk export
    public function collection() {
        return $this->data;
    }

    // Mendefinisikan header kolom file Excel
    public function headings(): array {
        return [
            'Title',
            'Assignee',
            'Due Date',
            'Time Tracked',
            'Status',
            'Priority'
        ];
    }

    // Map satu baris Todo ke array kolom
    public function map($todo): array {
        return [
            $todo->title,
            $todo->assignee,
            $todo->due_date,
            strval($todo->time_tracked ?? 0), 
            $todo->status,
            $todo->priority,
        ];
    }

    // Menambah baris summary row
    public function registerEvents(): array {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $totalTime = $this->data->sum('time_tracked'); 
                $totalRows = $this->data->count(); 

                $footerRow = $totalRows + 2;

                $event->sheet->setCellValue('A' . $footerRow, 'Total Todos: ' . $totalRows);
                
                $event->sheet->setCellValue('D' . $footerRow, 'Total Time: ' . $totalTime);

                $event->sheet->getStyle('A' . $footerRow . ':F' . $footerRow)->getFont()->setBold(true);
            },
        ];
    }
}