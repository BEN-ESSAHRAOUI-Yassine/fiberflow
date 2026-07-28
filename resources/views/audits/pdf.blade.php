<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ __('Rapport d\'Audit') }} #{{ $audit->id }} — {{ $project->name }}</title>
    <style>
        @page {
            margin: 20mm 15mm 25mm 15mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
            color: #333;
            line-height: 1.4;
        }
        h1 {
            font-size: 16pt;
            color: #1844D8;
            margin: 0 0 4pt 0;
        }
        h2 {
            font-size: 13pt;
            color: #1e3a5f;
            margin: 16pt 0 6pt 0;
            border-bottom: 2px solid #1844D8;
            padding-bottom: 3pt;
        }
        h3 {
            font-size: 10pt;
            color: #374151;
            margin: 10pt 0 4pt 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8pt;
            font-size: 8pt;
        }
        th {
            background-color: #1844D8;
            color: #fff;
            padding: 4pt 5pt;
            text-align: left;
            font-weight: bold;
            font-size: 7.5pt;
        }
        td {
            padding: 3pt 5pt;
            border: 1px solid #d1d5db;
        }
        tr:nth-child(even) {
            background-color: #f9fafb;
        }
        .header-table {
            border: none;
            margin-bottom: 12pt;
        }
        .header-table td {
            border: none;
            padding: 2pt 5pt;
        }
        .header-table td.label {
            font-weight: bold;
            color: #6b7280;
            width: 110pt;
        }
        .section {
            margin-bottom: 16pt;
        }
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 7pt;
            color: #9ca3af;
            padding: 8pt 0;
            border-top: 1px solid #e5e7eb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .text-muted {
            color: #9ca3af;
        }
        .font-mono {
            font-family: DejaVu Sans Mono, monospace;
        }
        .page-break-avoid {
            page-break-inside: avoid;
        }
        .grid-scores {
            width: 100%;
            margin-bottom: 8pt;
        }
        .grid-scores td {
            border: none;
            padding: 6pt;
            text-align: center;
        }
        .anomaly-count {
            display: inline-block;
            padding: 2pt 6pt;
            border-radius: 2pt;
            font-size: 8pt;
            font-weight: bold;
            margin-right: 4pt;
        }
        .count-critical { background: #fee2e2; color: #991b1b; }
        .count-warning { background: #fef9c3; color: #854d0e; }
        .count-info { background: #f3f4f6; color: #374151; }
    </style>
</head>
<body>

<div class="section">
<table class="header-table">
    <tr><td style="font-size:14pt;font-weight:bold;color:#1844D8;width:80%">{{ __('Rapport d\'Audit FTTH') }}</td>
        <td style="text-align:right;font-size:7pt;color:#9ca3af;">{{ date('d/m/Y H:i') }}</td></tr>
</table>
<table class="header-table">
    <tr><td class="label">{{ __('Audit') }} #</td><td>{{ $audit->id }}</td>
        <td class="label">{{ __('Projet') }}</td><td>{{ $project->name }}</td></tr>
    <tr><td class="label">{{ __('Statut') }}</td><td>{{ ucfirst($audit->status->value) }}</td>
        <td class="label">{{ __('Phase') }}</td><td>{{ $audit->phase_at_audit }}</td></tr>
    <tr><td class="label">{{ __('Type projet') }}</td><td>{{ $audit->project_type_at_audit }}</td>
        <td class="label">{{ __('Réalisé par') }}</td><td>{{ $audit->performer?->name ?? $audit->performed_by }}</td></tr>
    <tr><td class="label">{{ __('Débuté le') }}</td><td>{{ $audit->started_at?->format('d/m/Y H:i') ?? __('N/A') }}</td>
        <td class="label">{{ __('Terminé le') }}</td><td>{{ $audit->completed_at?->format('d/m/Y H:i') ?? __('N/A') }}</td></tr>
    @if ($audit->error_message)
    <tr><td class="label" style="color:#dc2626">{{ __('Erreur') }}</td><td colspan="3" style="color:#dc2626">{{ $audit->error_message }}</td></tr>
    @endif
</table>
</div>

@if ($audit->status->value === 'completed')

<div class="section page-break-avoid">
    <h2>{{ __('Scores Qualité') }}</h2>
    <table class="grid-scores">
        <tr>
            @php
                $scoreColor = fn($v) => $v >= 90 ? '#059669' : ($v >= 75 ? '#1844D8' : ($v >= 50 ? '#d97706' : '#dc2626'));
                $scoreBg = fn($v) => $v >= 90 ? '#d1fae5' : ($v >= 75 ? '#dbeafe' : ($v >= 50 ? '#fef3c7' : '#fee2e2'));
                $scoreLabel = fn($v) => $v >= 90 ? 'Excellent' : ($v >= 75 ? 'Good' : ($v >= 50 ? 'Acceptable' : 'Non-compliant'));
            @endphp
            @foreach ([
                ['score' => $audit->quality_score, 'label' => __('Global')],
                ['score' => $audit->connectivity_score, 'label' => __('Connectivité') . ' (40%)'],
                ['score' => $audit->coherence_score, 'label' => __('Cohérence') . ' (30%)'],
                ['score' => $audit->capacity_score, 'label' => __('Capacité') . ' (20%)'],
                ['score' => $audit->extensibility_score, 'label' => __('Extensibilité') . ' (10%)'],
            ] as $s)
            <td style="background:{{ $scoreBg($s['score']) }};border:1px solid {{ $scoreColor($s['score']) }};">
                <div style="font-size:14pt;font-weight:bold;color:{{ $scoreColor($s['score']) }};">{{ number_format($s['score'], 1) }}</div>
                <div style="font-size:7pt;color:#6b7280;">{{ $s['label'] }}</div>
                <div style="font-size:6pt;color:{{ $scoreColor($s['score']) }};">{{ __($scoreLabel($s['score'])) }}</div>
            </td>
            @endforeach
        </tr>
    </table>
</div>

@php $aiData = is_array($audit->ai_summary) ? $audit->ai_summary : null; @endphp
@if ($aiData)
<div class="section page-break-avoid">
    <h2>{{ __('Analyse IA') }}</h2>
    <table>
        <tr><td style="font-weight:bold;width:100pt;">{{ __('Résumé') }}</td><td>{{ $aiData['summary'] }}</td></tr>
        <tr><td style="font-weight:bold;">{{ __('Qualité') }}</td><td>{{ $aiData['quality'] }}</td></tr>
    </table>
    @if (!empty($aiData['observations']))
    <h3>{{ __('Observations') }}</h3>
    <table>
        @foreach ($aiData['observations'] as $obs)
        <tr><td style="padding-left:10pt;">&bull; {{ $obs }}</td></tr>
        @endforeach
    </table>
    @endif
    @if (!empty($aiData['risks']))
    <h3 style="color:#dc2626;">{{ __('Risques') }}</h3>
    <table>
        @foreach ($aiData['risks'] as $risk)
        <tr><td style="padding-left:10pt;color:#dc2626;">&bull; {{ $risk }}</td></tr>
        @endforeach
    </table>
    @endif
    @if (!empty($aiData['recommendations']))
    <h3 style="color:#1844D8;">{{ __('Recommandations') }}</h3>
    <table>
        @foreach ($aiData['recommendations'] as $rec)
        <tr><td style="padding-left:10pt;color:#1844D8;">&bull; {{ $rec }}</td></tr>
        @endforeach
    </table>
    @endif
    @if ($audit->model_used)
    <p style="font-size:7pt;color:#9ca3af;margin-top:4pt;">{{ __('Modèle') }}: {{ $audit->model_used }} @if ($audit->tokens_used) | {{ __('Jetons') }}: {{ $audit->tokens_used }} @endif</p>
    @endif
</div>
@elseif ($audit->ai_summary)
<div class="section page-break-avoid">
    <h2>{{ __('AI Summary') }}</h2>
    <p style="white-space:pre-wrap;">{{ $audit->ai_summary }}</p>
</div>
@endif

@if ($audit->network_statistics)
<div class="section page-break-avoid">
    <h2>{{ __('Anomalies') }}</h2>
    @php
        $anomalies = $audit->network_statistics['detailed']['anomalies'] ?? [];
        $critical = collect($anomalies)->where('severity', 'critical')->count();
        $warning = collect($anomalies)->where('severity', 'warning')->count();
        $info = collect($anomalies)->where('severity', 'info')->count();
    @endphp
    <table>
        <tr>
            <td style="font-weight:bold;width:80pt;">{{ __('Total') }}</td>
            <td>{{ count($anomalies) }} {{ __('anomalies') }}</td>
            <td style="font-weight:bold;width:80pt;">{{ __('Critiques') }}</td>
            <td><span class="anomaly-count count-critical">{{ $critical }}</span></td>
            <td style="font-weight:bold;width:80pt;">{{ __('Avertissements') }}</td>
            <td><span class="anomaly-count count-warning">{{ $warning }}</span></td>
            <td style="font-weight:bold;width:80pt;">{{ __('Infos') }}</td>
            <td><span class="anomaly-count count-info">{{ $info }}</span></td>
        </tr>
    </table>
    <p style="font-size:7pt;color:#9ca3af;">{{ __('Données détaillées disponibles dans l\'export Excel.') }}</p>
</div>

<div class="section page-break-avoid">
    <h2>{{ __('Couches réseau') }}</h2>
    <table>
        <thead><tr><th>{{ __('Couche') }}</th><th class="text-right">{{ __('Nombre') }}</th></tr></thead>
        <tbody>
            @foreach (collect($audit->network_statistics)->except(['total_fibers', 'used_fibers', 'spare_fibers', 'occupation_rate', 'detailed']) as $layer => $count)
            <tr><td>{{ $layer }}</td><td class="text-right">{{ $count }}</td></tr>
            @endforeach
        </tbody>
    </table>
</div>

@php
    $detailed = $audit->network_statistics['detailed'] ?? null;
    $fpb = $detailed['fibers_per_pbo'] ?? [];
    $feeders = $fpb['feeder_cables'] ?? [];
    $totalCapa = array_sum(array_column($feeders, 'capacity'));
    $totalUtile = $fpb['total_fiber_utile'] ?? 0;
    $totalDispo = $fpb['total_fiber_disponible'] ?? 0;
    $occRate = $totalCapa > 0 ? round($totalUtile / $totalCapa * 100) : 0;
@endphp

<div class="section page-break-avoid">
    <h2>{{ __('Fibre') }}</h2>
    <table>
        <tr>
            <td style="font-weight:bold;width:80pt;background:#d1fae5;">{{ __('Capacité') }}</td>
            <td style="width:80pt;background:#d1fae5;">{{ $totalCapa }}</td>
            <td style="font-weight:bold;width:80pt;background:#dbeafe;">{{ __('Utilisée') }}</td>
            <td style="width:80pt;background:#dbeafe;">{{ $totalUtile }}</td>
            <td style="font-weight:bold;width:80pt;background:#fef3c7;">{{ __('Disponible') }}</td>
            <td style="width:80pt;background:#fef3c7;">{{ $totalDispo }}</td>
            <td style="font-weight:bold;width:80pt;background:#f3f4f6;">{{ __('Occupation') }}</td>
            <td style="width:80pt;background:#f3f4f6;">{{ $occRate }}%</td>
        </tr>
    </table>
</div>

@if ($detailed && !empty($detailed['cables']['total_count']))
<div class="section page-break-avoid">
    <h2>{{ __('Câbles') }}</h2>
    <table>
        <tr>
            <td style="font-weight:bold;width:120pt;">{{ __('Total câbles') }}</td>
            <td>{{ $detailed['cables']['total_count'] }}</td>
            <td style="font-weight:bold;width:120pt;">{{ __('Longueur totale') }}</td>
            <td>{{ number_format($detailed['cables']['total_length_m'], 1) }} m</td>
        </tr>
    </table>
</div>
@endif

@if ($detailed && !empty($detailed['equipment']['optical_boxes']['total']))
<div class="section page-break-avoid">
    <h2>{{ __('Boîtes Optiques') }}</h2>
    <table>
        <tr>
            <td style="font-weight:bold;width:120pt;">{{ __('Total') }}</td>
            <td>{{ $detailed['equipment']['optical_boxes']['total'] }}</td>
            <td style="font-weight:bold;width:120pt;">{{ __('Cassettes') }}</td>
            <td>{{ $detailed['equipment']['optical_boxes']['total_cassettes'] }}</td>
        </tr>
    </table>
</div>
@endif

@if (isset($detailed['logements']))
<div class="section page-break-avoid">
    <h2>{{ __('Logements') }}</h2>
    <table>
        <tr>
            <td style="font-weight:bold;width:80pt;background:#f3e8ff;">{{ __('Total') }}</td>
            <td style="width:60pt;background:#f3e8ff;">{{ $detailed['logements']['logements']['total'] }}</td>
            <td style="font-weight:bold;width:80pt;">{{ __('Capacité max') }}</td>
            <td style="width:60pt;">{{ $detailed['logements']['logements']['max_capacity'] }}</td>
            <td style="font-weight:bold;width:80pt;background:#dbeafe;">{{ __('Connecté') }}</td>
            <td style="width:60pt;background:#dbeafe;">{{ $detailed['logements']['connected'] }}</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">{{ __('Occupation') }}</td>
            <td>{{ $detailed['logements']['logements']['occupation_rate'] }}%</td>
            <td style="font-weight:bold;">{{ __('Zones SRO') }}</td>
            <td>{{ $detailed['logements']['sro_zone_count'] }}</td>
            <td style="font-weight:bold;">{{ __('Zones PBO') }}</td>
            <td>{{ $detailed['logements']['pbo_zone_count'] }}</td>
        </tr>
    </table>
    <table>
        <tr>
            <td style="font-weight:bold;background:#f3e8ff;">{{ __('Prises Hab.') }}</td>
            <td style="background:#f3e8ff;">{{ $detailed['addresses']['prises_habitation'] ?? 0 }}</td>
            <td style="font-weight:bold;background:#e0e7ff;">{{ __('Prises Pro.') }}</td>
            <td style="background:#e0e7ff;">{{ $detailed['addresses']['prises_professionnelles'] ?? 0 }}</td>
            <td style="font-weight:bold;background:#dbeafe;">{{ __('Locaux Hab.') }}</td>
            <td style="background:#dbeafe;">{{ $detailed['addresses']['locaux_habitation'] ?? 0 }}</td>
            <td style="font-weight:bold;background:#fef3c7;">{{ __('Immeubles Neufs') }}</td>
            <td style="background:#fef3c7;">{{ $detailed['addresses']['immeubles_neufs'] ?? 0 }}</td>
        </tr>
    </table>
</div>
@endif

@endif
@endif

<div class="footer">
    {{ __('Généré le') }} {{ date('d/m/Y \à H:i') }} — FiberFlow / page {{ '{PAGE_NUM}' }} / {{ '{PAGE_COUNT}' }}
</div>

</body>
</html>
