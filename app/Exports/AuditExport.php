<?php

namespace App\Exports;

use App\Models\Audit;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        private readonly Audit $audit,
    ) {}

    public function sheets(): array
    {
        $detailed = $this->audit->network_statistics['detailed'] ?? [];

        return [
            new AnomaliesSheet($detailed),
            new CablesSheet($detailed),
            new FibreSheet($detailed),
            new OpticalBoxesSheet($detailed),
            new SupportsSheet($detailed),
            new ConduitsSheet($detailed),
            new LogementsSheet($detailed),
        ];
    }
}

class AnomaliesSheet implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private Collection $rows;

    public function __construct(array $detailed)
    {
        $severityOrder = ['critical' => 0, 'warning' => 1, 'info' => 2];
        $this->rows = collect($detailed['anomalies'] ?? [])
            ->sortBy(fn ($a) => $severityOrder[$a['severity']] ?? 99)
            ->values();
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['SHP', 'Sévérité', 'Type', 'Message', 'Solution'];
    }

    public function map($anomaly): array
    {
        return [
            $anomaly['shp'] ?? '-',
            $anomaly['severity'] ?? '-',
            $anomaly['type'] ?? '-',
            $anomaly['message'] ?? '-',
            $anomaly['solution'] ?? '-',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:'.$sheet->getHighestColumn(1).'1');
            },
        ];
    }

    public function title(): string
    {
        return 'Anomalies';
    }
}

class CablesSheet implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private Collection $rows;

    public function __construct(array $detailed)
    {
        $this->rows = collect($detailed['cables']['by_reference'] ?? [])->sortByDesc('count')->values();
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['Désignation', 'RF Code', 'Fabricant', 'FO', 'Modulo', 'Installation', 'Nb', 'Carto (m)', 'Ajusté (m)'];
    }

    public function map($ref): array
    {
        return [
            $ref['designation'] ?? '-',
            $ref['rf_code'] ?? '-',
            $ref['manufacturer'] ?? '-',
            $ref['fiber_count'] ?? '-',
            $ref['modulo'] ?? '-',
            $ref['installation'] ?? '-',
            $ref['count'] ?? 0,
            round($ref['carto_length_m'] ?? 0, 1),
            round($ref['adjusted_length_m'] ?? 0, 1),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:'.$sheet->getHighestColumn(1).'1');
            },
        ];
    }

    public function title(): string
    {
        return 'Câbles';
    }
}

class FibreSheet implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private Collection $rows;

    public function __construct(array $detailed)
    {
        $fpb = $detailed['fibers_per_pbo'] ?? [];
        $this->rows = collect($fpb['feeder_cables'] ?? [])->values();
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['Câble', 'Capacité', 'Utile', 'Disponible', 'Nb Zones PBO', 'Zones'];
    }

    public function map($feeder): array
    {
        $zones = collect($feeder['zones'] ?? [])->pluck('zp_code')->implode(', ');

        return [
            $feeder['cable_code'] ?? '-',
            $feeder['capacity'] ?? 0,
            $feeder['total_utile'] ?? 0,
            $feeder['total_disponible'] ?? 0,
            count($feeder['zones'] ?? []),
            $zones,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:'.$sheet->getHighestColumn(1).'1');
            },
        ];
    }

    public function title(): string
    {
        return 'Fibre';
    }
}

class OpticalBoxesSheet implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private Collection $rows;

    public function __construct(array $detailed)
    {
        $this->rows = collect($detailed['equipment']['optical_boxes']['by_reference'] ?? [])->sortByDesc('count')->values();
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['Désignation', 'RF Code', 'Fabricant', 'Type Logique', 'Statut', 'Avancement', 'Nb', 'Cassettes'];
    }

    public function map($ref): array
    {
        return [
            $ref['designation'] ?? '-',
            $ref['rf_code'] ?? '-',
            $ref['manufacturer'] ?? '-',
            $ref['logical_type'] ?? '-',
            $ref['statut'] ?? '-',
            $ref['avancement'] ?? '-',
            $ref['count'] ?? 0,
            $ref['cassettes'] ?? 0,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:'.$sheet->getHighestColumn(1).'1');
            },
        ];
    }

    public function title(): string
    {
        return 'Boîtes optiques';
    }
}

class SupportsSheet implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private Collection $rows;

    private array $ownerNames;

    public function __construct(array $detailed)
    {
        $this->ownerNames = $detailed['supports']['organismes'] ?? [];
        $this->rows = collect();

        $tp = $detailed['supports']['technical_points'] ?? [];
        foreach ($tp['by_statut'] ?? [] as $statut => $group) {
            foreach ($group['by_owner'] as $owner => $types) {
                $this->rows->push([
                    'statut' => $statut,
                    'owner' => $this->ownerNames[$owner] ?? $owner,
                    'A' => $types['A'] ?? 0,
                    'C' => $types['C'] ?? 0,
                    'F' => $types['F'] ?? 0,
                    'I' => $types['I'] ?? 0,
                    'Z' => $types['Z'] ?? 0,
                    'total' => array_sum($types),
                ]);
            }
        }
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['Statut', 'Propriétaire', 'Appui', 'Chambre', 'Façade', 'Immeuble', 'Autre', 'Total'];
    }

    public function map($row): array
    {
        return [
            $row['statut'],
            $row['owner'],
            $row['A'],
            $row['C'],
            $row['F'],
            $row['I'],
            $row['Z'],
            $row['total'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:'.$sheet->getHighestColumn(1).'1');
            },
        ];
    }

    public function title(): string
    {
        return 'Supports';
    }
}

class ConduitsSheet implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private Collection $rows;

    private array $ownerNames;

    public function __construct(array $detailed)
    {
        $this->ownerNames = $detailed['supports']['organismes'] ?? [];
        $this->rows = collect();

        $cd = $detailed['supports']['conduits'] ?? [];
        foreach ($cd['by_statut'] ?? [] as $statut => $group) {
            foreach ($group['by_owner'] as $owner => $lengths) {
                $ug = $lengths['underground_length'] ?? 0;
                $ae = $lengths['aerial_length'] ?? 0;
                $fo = $lengths['facade_other_length'] ?? 0;
                $this->rows->push([
                    'statut' => $statut,
                    'owner' => $this->ownerNames[$owner] ?? $owner,
                    'underground' => $ug,
                    'aerial' => $ae,
                    'facade' => $fo,
                    'total' => $ug + $ae + $fo,
                ]);
            }
        }
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['Statut', 'Propriétaire', 'Souterrain (m)', 'Aérien (m)', 'Façade/Autre (m)', 'Total (m)'];
    }

    public function map($row): array
    {
        return [
            $row['statut'],
            $row['owner'],
            round($row['underground'], 1),
            round($row['aerial'], 1),
            round($row['facade'], 1),
            round($row['total'], 1),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:'.$sheet->getHighestColumn(1).'1');
            },
        ];
    }

    public function title(): string
    {
        return 'Conduites';
    }
}

class LogementsSheet implements FromCollection, ShouldAutoSize, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private Collection $rows;

    public function __construct(array $detailed)
    {
        $this->rows = collect();

        $log = $detailed['logements'] ?? [];
        $addr = $detailed['addresses'] ?? [];
        $typeIm = $addr['by_type_immeuble'] ?? [];

        $this->rows->push([
            'section' => 'Logements',
            'total' => $log['logements']['total'] ?? 0,
            'max_capacity' => $log['logements']['max_capacity'] ?? 0,
            'connected' => $log['connected'] ?? 0,
            'occupation_rate' => ($log['logements']['occupation_rate'] ?? 0).'%',
            'sro_zones' => $log['sro_zone_count'] ?? 0,
            'pbo_zones' => $log['pbo_zone_count'] ?? 0,
        ]);

        foreach ($typeIm as $code => $count) {
            $this->rows->push([
                'section' => 'Type Immeuble: '.($code === 'I' ? 'Immeuble' : ($code === 'P' ? 'Pavillon' : $code)),
                'total' => $count,
                'max_capacity' => '-',
                'connected' => '-',
                'occupation_rate' => '-',
                'sro_zones' => '-',
                'pbo_zones' => '-',
            ]);
        }

        $this->rows->push([
            'section' => 'Prises Habitation',
            'total' => $addr['prises_habitation'] ?? 0,
            'max_capacity' => '-',
            'connected' => '-',
            'occupation_rate' => '-',
            'sro_zones' => '-',
            'pbo_zones' => '-',
        ]);

        $this->rows->push([
            'section' => 'Prises Professionnelles',
            'total' => $addr['prises_professionnelles'] ?? 0,
            'max_capacity' => '-',
            'connected' => '-',
            'occupation_rate' => '-',
            'sro_zones' => '-',
            'pbo_zones' => '-',
        ]);
    }

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return ['Section', 'Total', 'Capacité Max', 'Connecté', 'Occupation', 'Zones SRO', 'Zones PBO'];
    }

    public function map($row): array
    {
        return [
            $row['section'],
            $row['total'],
            $row['max_capacity'],
            $row['connected'],
            $row['occupation_rate'],
            $row['sro_zones'],
            $row['pbo_zones'],
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '2563EB']],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:'.$sheet->getHighestColumn(1).'1');
            },
        ];
    }

    public function title(): string
    {
        return 'Logements';
    }
}
