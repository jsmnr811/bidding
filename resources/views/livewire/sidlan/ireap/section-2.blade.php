<?php

use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use App\Services\SidlanAPIServices;

new class extends Component {
    public $chartData = [];
    public $underBusinessPlanPreparationItems = [];
    public $subprojectModal = false;
    public $consolidatedTableData = [];
    public $tableData = [];
    public $filterKey = 'All';
    public $loader = false;
    public string $modalTitle = '';
    public string $modalSubtitle = '';
    public array $dataSets = [];

    public function mount(): void
    {
        $this->loader = true;
        $this->initChartData();
    }

    private function initData()
    {
        $underBusinessPlanPreparation = $this->underBusinessPlanPreparation();
        $dataSets['underBusinessPlanPreparation'] = [
            'title' => 'Under Business Plan Preparation',
            'subject_count' => $underBusinessPlanPreparation['count'],
            'beyond_timeline_count' => $underBusinessPlanPreparation['beyondTimelineCount'],
            'key' => 'underBusinessPlanPreparation',
            'average_difference_days' => $underBusinessPlanPreparation['average_difference_days'],

        ];
        $dataSets['forRPABApproval'] = [
            'title' => 'Under Review /  For RPAB Approval',
            'subject_count' => 0 + 11,
            'beyond_timeline_count' => 12,
            'key' => 'forRPABApproval',
        ];
        $dataSets['rpabApproved'] = [
            'title' => 'RPAB Approved (For NOL 1)',
            'subject_count' => 13,
            'beyond_timeline_count' => 14,
            'key' => 'rpabApproved',
        ];

        $this->consolidatedTableData['underBusinessPlanPreparation']['subprojectItems'] = $underBusinessPlanPreparation['items'] ?? [];
        $this->consolidatedTableData['underBusinessPlanPreparation']['beyondTimelineItems'] = $underBusinessPlanPreparation['beyondTimeline'] ?? [];
        $this->dataSets = $dataSets;

        return $dataSets;
    }

    private function underBusinessPlanPreparation(): array
    {
        $apiService = new SidlanAPIServices();
        $irZeroTwoData = $apiService->executeRequest(['dataset_id' => 'ir-01-002']);
        $zeroTwo = collect($irZeroTwoData);
        $items = [];

        if ($this->filterKey == 'All') {
            $items = $zeroTwo->filter(
                fn($item) =>
                $item['stage'] === 'Pre-procurement' &&
                    $item['specific_status'] === 'Subproject Confirmed' &&
                    !empty($item['subproject_confirmed']) &&
                    empty($item['business_plan_packaged'])
            )->map(function ($item) {
                return collect($item)->only([
                    'cluster',
                    'region',
                    'province',
                    'city_municipality',
                    'proponent',
                    'project_name',
                    'subproject_confirmed',
                    'project_type',
                    'stage',
                    'specific_status'
                ]);
            });
        } elseif (in_array($this->filterKey, ['Luzon A', 'Luzon B', 'Visayas', 'Mindanao'])) {
            $items = $zeroTwo->filter(
                fn($item) =>
                $item['stage'] === 'Pre-procurement' &&
                    $item['specific_status'] === 'Subproject Confirmed' &&
                    !empty($item['subproject_confirmed']) &&
                    empty($item['business_plan_packaged']) &&
                    $item['cluster'] === $this->filterKey
            )->map(function ($item) {
                return collect($item)->only([
                    'cluster',
                    'region',
                    'province',
                    'city_municipality',
                    'proponent',
                    'project_name',
                    'subproject_confirmed',
                    'project_type',
                    'stage',
                    'specific_status'
                ]);
            });
        } else {
            $items = $zeroTwo->filter(
                fn($item) =>
                $item['stage'] === 'Pre-procurement' &&
                    $item['specific_status'] === 'Subproject Confirmed' &&
                    !empty($item['subproject_confirmed']) &&
                    empty($item['business_plan_packaged']) &&
                    $item['region'] === $this->filterKey
            )->map(function ($item) {
                return collect($item)->only([
                    'cluster',
                    'region',
                    'province',
                    'city_municipality',
                    'proponent',
                    'project_name',
                    'subproject_confirmed',
                    'project_type',
                    'stage',
                    'specific_status'
                ]);
            });
        }

        $now = now();

        // Add date_difference to each item
        $items = $items->map(function ($item) use ($now) {
            $confirmedDate = Carbon::parse($item['subproject_confirmed']);
            $dateDiff = (int) $confirmedDate->diffInDays($now); // ensure integer

            // Format the date as "Jan 1, 2000"
            $formattedDate = $confirmedDate->format('M j, Y');

            return $item->merge([
                'date_difference' => $dateDiff,
                'subproject_confirmed' => $formattedDate,
            ]);
        });


        $beyondTimeline = $items->filter(function ($item) use ($now) {
            $confirmedDate = Carbon::parse($item['subproject_confirmed']);
            return $confirmedDate->lt($now) && $confirmedDate->diffInDays($now) > 204;
        });

        $averageDifferenceDays = $beyondTimeline->avg('date_difference');
        $averageDifferenceDays = $averageDifferenceDays ? round($averageDifferenceDays) : 0;

        return [
            'items' => $items,
            'count' => $items->count(),
            'beyondTimeline' => $beyondTimeline,
            'beyondTimelineCount' => $beyondTimeline->count(),
            'average_difference_days' => $averageDifferenceDays,

        ];
    }


    public function updatedFilterKey(): void
    {
        $this->loader = true;
        $this->initChartData();
    }

    private function initChartData(): void
    {
        $this->chartData = $this->initData();
        $this->dispatch('generateChart', ['chartData' => $this->chartData]);
    }

    #[On('barClicked')]
    public function barClicked($key, $type): void
    {
        $innerKey = $type ? 'beyondTimelineItems' : 'subprojectItems';
        if (isset($this->consolidatedTableData[$key])) {
            $this->tableData = $this->consolidatedTableData[$key][$innerKey];
        } else {
            $this->tableData = [];
        }
        $this->modalSubtitle = $this->dataSets[$key]['title'] ?? '';
        if ($innerKey === 'beyondTimelineItems') {
            $this->modalSubtitle .= ' (No. of SPs Beyond Timeline)';
        }
        $this->subprojectModal = true;
    }

    public function placeholder(): View
    {
        return view('livewire.sidlan.ireap.placeholder.section-2');
    }
};

?>

<div>
    <div class="row row-cols-1 row-gap-4 mt-4">
        <div class="col">
            <div class="tile-container h-100 d-flex flex-column">
                <div class="tile-title d-flex flex-column flex-lg-row row-gap-2 justify-content-between align-items-start"
                    style="font-size: 1.2rem;">
                    <span>I-REAP Subprojects Currently in the Pipeline (Number of Subprojects by Status)</span>
                    <div class="d-flex flex-row gap-2 align-items-center small">
                        <div class="fw-normal">Show:</div>
                        <select wire:model.live="filterKey" class="form-select filter-dropdown pe-lg-5">
                            <option value="All">All</option>
                            <optgroup label="Clusterwide">
                                <option value="Luzon A">Luzon A</option>
                                <option value="Luzon B">Luzon B</option>
                                <option value="Visayas">Visayas</option>
                                <option value="Mindanao">Mindanao</option>
                            </optgroup>
                            <optgroup label="Regionwide">
                                <option value="Cordillera Administrative Region (CAR)" data-group="region">CAR</option>
                                <option value="Ilocos Region (Region I)" data-group="region">Region 01</option>
                                <option value="Cagayan Valley (Region II)" data-group="region">Region 02</option>
                                <option value="Central Luzon (Region III)" data-group="region">Region 03</option>
                                <option value="CALABARZON (Region IV-A)" data-group="region">Region 04A</option>
                                <option value="MIMAROPA (Region IV-B)" data-group="region">Region 04B</option>
                                <option value="Bicol Region (Region V)" data-group="region">Region 05</option>
                                <option value="Western Visayas (Region VI)" data-group="region">Region 06</option>
                                <option value="Central Visayas (Region VII)" data-group="region">Region 07</option>
                                <option value="Eastern Visayas (Region VIII)" data-group="region">Region 08</option>
                                <option value="Zamboanga Peninsula (Region IX)" data-group="region">Region 09</option>
                                <option value="Northern Mindanao (Region X)" data-group="region">Region 10</option>
                                <option value="Davao Region (Region XI)" data-group="region">Region 11</option>
                                <option value="SOCCSKSARGEN (Region XII)" data-group="region">Region 12</option>
                                <option value="Caraga (Region XIII)" data-group="region">Region 13</option>
                                <option value="Bangsamoro Autonomous Region of Muslim Mindanao (BARMM)"
                                    data-group="region">
                                    BARMM</option>
                            </optgroup>
                        </select>
                    </div>
                </div>


                <div wire:ignore class="tile-content position-relative overflow-hidden chart-container"
                    style="height: 400px;">
                    @if ($loader)
                    <div class="loading-dots my-4 text-center">
                        <span class="dot"></span>
                        <span class="dot"></span>
                        <span class="dot"></span>
                    </div>


                    @endif
                    <canvas class="tile-chart position-absolute top-0 start-0 w-100 h-100"
                        id="subproject-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    @if ($subprojectModal)
    <div class="modal fade show" id="helloModal" tabindex="-1" aria-modal="true" role="dialog" style="display: block;" aria-labelledby="helloModalLabel" aria-hidden="false">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">

                <div class="modal-header position-relative flex-column align-items-start pb-0" style="border-bottom: none;">
                    <h5 class="modal-title mb-0 fw-bold text-primary" id="helloModalLabel">
                        I-REAP Subprojects in the Pipeline (Number of Subprojects by Status)
                    </h5>
                    <small class="text-warning fw-semibold" style="font-size: 1rem;">
                        {{ $modalSubtitle }}
                    </small>
                    <button type="button" class="btn-close position-absolute top-0 end-0 mt-2 me-2" wire:click='$set("subprojectModal", false)' aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    @if (count($tableData) > 0)
                    <div style="overflow-x: auto;">
                        <table class="table table-hover fix-header-table small mb-0" id="subprojectTable" style="width: auto; min-width: 100%;">
                            <thead>
                                <tr>
                                    <th style="white-space: nowrap;">Cluster</th>
                                    <th style="white-space: nowrap;">Region</th>
                                    <th style="white-space: nowrap;">Province</th>
                                    <th style="white-space: nowrap;">City/Municipality</th>
                                    <th style="white-space: nowrap;">Proponent</th>
                                    <th style="white-space: nowrap;">SP Name</th>
                                    <th style="white-space: nowrap;">Type</th>
                                    <th style="white-space: nowrap;">Cost</th>
                                    <th style="white-space: nowrap;">Stage</th>
                                    <th style="white-space: nowrap;">Status</th>
                                    <th style="white-space: nowrap;">No. of days</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tableData as $data)
                                <tr>
                                    <td style="white-space: nowrap;">{{ $data['cluster'] }}</td>
                                    <td style="white-space: nowrap;">{{ $data['region'] }}</td>
                                    <td style="white-space: nowrap;">{{ $data['province'] }}</td>
                                    <td style="white-space: nowrap;">{{ $data['city_municipality'] }}</td>
                                    <td style="white-space: nowrap;">{{ $data['proponent'] }}</td>
                                    <td style="white-space: nowrap;">{{ $data['project_name'] }}</td>
                                    <td style="white-space: nowrap;">{{ $data['project_name'] }}</td>
                                    <td style="white-space: nowrap;"></td>
                                    <td style="white-space: nowrap;">{{ $data['stage'] }}</td>
                                    <td style="white-space: nowrap;">{{ $data['specific_status'] }}</td>
                                    <td style="white-space: nowrap;">{{ $data['date_difference'] . ' days from Date of confirmation (' . $data['subproject_confirmed'] . ')' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p>No data found.</p>
                    @endif
                </div>

            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show"></div>
    @endif

</div>
<style>
    .loading-dots {
        display: inline-flex;
        gap: 0.5rem;
        justify-content: center;
        align-items: center;
    }

    .loading-dots .dot {
        width: 0.75rem;
        height: 0.75rem;
        background-color: #6c757d;
        border-radius: 50%;
        animation: bounce 0.8s infinite alternate;
    }

    .loading-dots .dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .loading-dots .dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes bounce {
        from {
            transform: translateY(0);
            opacity: 0.5;
        }

        to {
            transform: translateY(-0.5rem);
            opacity: 1;
        }
    }
</style>
@script
<script>
    // Keep chart instance globally
    window.chartInstance = null;

    window.ChartOne = function(chartData) {
        const canvas = document.getElementById('subproject-chart');

        if (!canvas) return; // prevent errors if canvas not in DOM

        const ctx = canvas.getContext('2d');

        // Destroy previous chart if exists
        if (window.chartInstance) {
            window.chartInstance.destroy();
            window.chartInstance = null;
        }

        const groupKeys = Object.keys(chartData);
        console.log(chartData);

        const averageDiff = (() => {
            let total = 0;
            let count = 0;
            groupKeys.forEach(k => {
                if (chartData[k].average_difference_days) {
                    total += chartData[k].average_difference_days;
                    count++;
                }
            });
            return count ? Math.round(total / count) : 0;
        })();

        window.chartInstance = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: groupKeys.map(key => chartData[key].title),
                datasets: [{
                        label: 'No. of Subprojects',
                        backgroundColor: '#0047e0',
                        data: groupKeys.map(key => chartData[key].subject_count),
                        borderRadius: 8,
                    },
                    {
                        label: 'No. of Subprojects Beyond Timeline',
                        backgroundColor: '#fa2314',
                        data: groupKeys.map(key => chartData[key].beyond_timeline_count),
                        borderRadius: 8,

                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true
                    },
                    tooltip: {
                        callbacks: {
                            label: ctx => `${ctx.dataset.label}: ${ctx.parsed.y}`
                        }
                    },
                    datalabels: {
                        display: true,
                        color: '#000',
                        font: {
                            // weight: 'bold',
                            size: 14
                        },
                        align: 'end',
                        anchor: 'end',
                        textAlign: 'center',
                        formatter: function(value, context) {
                            if (context.datasetIndex === 1 && value > 0) {
                                return [
                                    `${value}`,
                                    `(${averageDiff} days vs`,
                                    `204 days timeline)`
                                ];

                            }
                            return value > 0 ? `${value}` : '';
                        }
                    }
                },
                onClick: (evt, elements) => {
                    if (!elements.length) return;
                    const element = elements[0];
                    const index = element.index;
                    const key = groupKeys[index];
                    const datasetIndex = element.datasetIndex;
                    Livewire.dispatch('barClicked', {
                        key,
                        type: datasetIndex
                    });
                }
            },
            plugins: [ChartDataLabels]
        });
    };

    // Trigger chart only when Livewire dispatches
    Livewire.on('generateChart', data => {
        setTimeout(() => {
            if (data[0] && data[0].chartData) {
                window.ChartOne(data[0].chartData);
                $wire.set('loader', false);
            }
        }, 50); // 50ms delay ensures canvas exists
        // $wire.set('loader', false);
    });
</script>
@endscript