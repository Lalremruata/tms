<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $udiseCode;
    protected $schoolInfo;

    /**
     * Create a new export instance.
     *
     * @param string $udiseCode
     * @return void
     */
    public function __construct(string $udiseCode)
    {
        $this->udiseCode = $udiseCode;

        // Get school information
        $this->schoolInfo = DB::table('mizoram115.school_master as a')
            ->join('mizoram115.mst_district as b', 'a.district_cd', '=', 'b.udise_district_code')
            ->join('mizoram115.mst_block as c', 'a.block_cd', '=', 'c.udise_block_code')
            ->select(
                'b.district_name',
                'c.block_name',
                'a.school_name',
                'a.udise_sch_code'
            )
            ->where('a.udise_sch_code', $this->udiseCode)
            ->first();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return DB::table('sms.student_profile as a')
            ->select(
                'a.userid',
                'a.student_pen',
                'a.student_name',
                DB::raw("CASE
                    WHEN gender = '1' THEN 'Male'
                    WHEN gender = '2' THEN 'Female'
                    WHEN gender = '3' THEN 'Trans.'
                    ELSE gender
                END as gender"),
                'a.student_dob',
                'a.father_name',
                'a.mother_name',
                'a.mobile_no_1',
                DB::raw("CASE
                    WHEN a.pres_class = -1 THEN 'Class UKG/KG2/PP1'
                    WHEN a.pres_class = -2 THEN 'Class LKG/KG1/PP2'
                    WHEN a.pres_class = -3 THEN 'Class Nursery/KG/PP3'
                    WHEN a.pres_class = 1 THEN 'Class 1'
                    WHEN a.pres_class = 2 THEN 'Class 2'
                    WHEN a.pres_class = 3 THEN 'Class 3'
                    WHEN a.pres_class = 4 THEN 'Class 4'
                    WHEN a.pres_class = 5 THEN 'Class 5'
                    WHEN a.pres_class = 6 THEN 'Class 6'
                    WHEN a.pres_class = 7 THEN 'Class 7'
                    WHEN a.pres_class = 8 THEN 'Class 8'
                    WHEN a.pres_class = 9 THEN 'Class 9'
                    WHEN a.pres_class = 10 THEN 'Class 10'
                    WHEN a.pres_class = 11 THEN 'Class 11'
                    WHEN a.pres_class = 12 THEN 'Class 12'
                    ELSE 'Unknown'
                END as class"),
                DB::raw("CASE
                    WHEN section_id::text = '1' THEN 'Section A'
                    WHEN section_id::text = '2' THEN 'Section B'
                    WHEN section_id::text = '3' THEN 'Section C'
                    WHEN section_id::text = '4' THEN 'Section D'
                    WHEN section_id::text = '5' THEN 'Section E'
                    WHEN section_id::text = '6' THEN 'Section F'
                    ELSE section_id::text
                END as section"),
                DB::raw("CASE
                    WHEN stud_status = 'E' THEN 'Enrolled'
                    WHEN stud_status = 'P' THEN 'Pending'
                    ELSE stud_status
                END as status")
            )
            ->where('a.udise_cd', $this->udiseCode)
            ->whereRaw("a.stud_status IN ('E', 'P')")
            ->orderBy('a.pres_class')
            ->orderBy('a.student_name')
            ->get();
    }

    /**
     * @var object $student
     */
    public function map($student): array
    {
        // Map the student data to the row values
        return [
            $student->userid,
            $student->student_pen ?? '',
            $student->student_name,
            $student->gender,
            $student->student_dob,
            $student->father_name,
            $student->mother_name,
            $student->mobile_no_1,
            $student->class,
            $student->section,
            $student->status,
        ];
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Student Code (Temporary)',
            'Student PEN',
            'Student Name',
            'Gender',
            'Date of Birth',
            'Father Name',
            'Mother Name',
            'Mobile No.',
            'Class',
            'Section',
            'Status'
        ];
    }

    /**
     * @param Worksheet $sheet
     */
//    public function styles(Worksheet $sheet)
//    {
//        // Add school information headers
//        $sheet->mergeCells('A1:K1');
//        $sheet->setCellValue('A1', 'Student List');
//
//        $sheet->mergeCells('A2:K2');
//        $sheet->setCellValue('A2', 'School: ' . ($this->schoolInfo->school_name ?? 'N/A'));
//
//        $sheet->mergeCells('A3:K3');
//        $sheet->setCellValue('A3', 'District: ' . ($this->schoolInfo->district_name ?? 'N/A') . ' | Block: ' . ($this->schoolInfo->block_name ?? 'N/A') . ' | UDISE Code: ' . ($this->schoolInfo->udise_sch_code ?? 'N/A'));
//
//        $sheet->mergeCells('A4:K4');
//        $sheet->setCellValue('A4', 'Generated on: ' . now()->format('d-m-Y H:i:s'));
//
//        // Style the headers and school info
//        $sheet->getStyle('A1:K1')->getFont()->setBold(true)->setSize(14);
//        $sheet->getStyle('A2:K4')->getFont()->setBold(true);
//        $sheet->getStyle('A5:K5')->getFont()->setBold(true);
//
//        // Add background color to the header row
//        $sheet->getStyle('A5:K5')->getFill()
//            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
//            ->getStartColor()->setRGB('4CAF50');
//
//        $sheet->getStyle('A5:K5')->getFont()->getColor()->setRGB('FFFFFF');
//
//        // Auto-fit columns
//        foreach(range('A', 'K') as $column) {
//            $sheet->getColumnDimension($column)->setAutoSize(true);
//        }
//
//        return [
//            5 => ['font' => ['bold' => true]],
//        ];
//    }
}
