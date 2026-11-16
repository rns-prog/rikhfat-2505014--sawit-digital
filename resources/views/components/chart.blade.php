@props([
    'id',
    'type',
    'labels',
    'values',
    'unit' => '',
    'sparkline' => false,
    'datasets' => null,
])

<canvas
    id="{{ $id }}"
    data-chart
    data-chart-type="{{ $type }}"
    data-chart-labels="{{ json_encode($labels, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
    data-chart-values="{{ json_encode($values, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
    data-chart-unit="{{ $unit }}"
    data-chart-sparkline="{{ $sparkline ? 'true' : 'false' }}"
    @if ($datasets !== null)
        data-chart-datasets="{{ json_encode($datasets, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_TAG) }}"
    @endif
    {{ $attributes }}
></canvas>
