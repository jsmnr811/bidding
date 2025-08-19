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
        ];
        $dataSets['secondDummyDataSet'] = [
            'title' => 'Second Data Set',
            'subject_count' => 0 + 11,
            'beyond_timeline_count' => 12,
            'key' => 'secondDummyDataSet',
        ];
        $dataSets['thirdDummyDataSet'] = [
            'title' => 'Third Data Set',
            'subject_count' => 13,
            'beyond_timeline_count' => 14,
            'key' => 'thirdDataSet',
        ];

        $this->consolidatedTableData['underBusinessPlanPreparation']['subprojectItems'] = $underBusinessPlanPreparation['items'] ?? [];
        $this->consolidatedTableData['underBusinessPlanPreparation']['beyondTimelineItems'] = $underBusinessPlanPreparation['beyondTimeline'] ?? [];

        return $dataSets;
    }

    private function underBusinessPlanPreparation(): array
    {
        $apiService = new SidlanAPIServices();
        $irZeroTwoData = $apiService->executeRequest(['dataset_id' => 'ir-01-002']);
        $zeroTwo = collect($irZeroTwoData);
        $items = [];
        if ($this->filterKey == 'All') {
            $items = $zeroTwo->filter(fn($item) => $item['stage'] === 'Pre-procurement' && $item['specific_status'] === 'Subproject Confirmed' && !empty($item['subproject_confirmed']) && empty($item['business_plan_packaged']))->map(fn($item) => collect($item)->only(['cluster', 'region', 'province', 'city_municipality', 'proponent', 'project_name', 'subproject_confirmed']));
        } elseif (in_array($this->filterKey, ['Luzon A', 'Luzon B', 'Visayas', 'Mindanao'])) {
            $items = $zeroTwo->filter(fn($item) => $item['stage'] === 'Pre-procurement' && $item['specific_status'] === 'Subproject Confirmed' && !empty($item['subproject_confirmed']) && empty($item['business_plan_packaged']) && $item['cluster'] === $this->filterKey)->map(fn($item) => collect($item)->only(['cluster', 'region', 'province', 'city_municipality', 'proponent', 'project_name', 'subproject_confirmed']));
        } else {
            $items = $zeroTwo->filter(fn($item) => $item['stage'] === 'Pre-procurement' && $item['specific_status'] === 'Subproject Confirmed' && !empty($item['subproject_confirmed']) && empty($item['business_plan_packaged']) && $item['region'] === $this->filterKey)->map(fn($item) => collect($item)->only(['cluster', 'region', 'province', 'city_municipality', 'proponent', 'project_name', 'subproject_confirmed']));
        }

        $now = now();

        $beyondTimeline = $items->filter(function ($item) use ($now) {
            $confirmedDate = Carbon::parse($item['subproject_confirmed']);
            return $now->diffInDays($confirmedDate) > 204;
        });

        return [
            'items' => $items,
            'count' => $items->count(),
            'beyondTimeline' => $beyondTimeline,
            'beyondTimelineCount' => $beyondTimeline->count(),
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
                @if ($loader)
                    loading
                @endif
                <div wire:ignore class="tile-content position-relative overflow-hidden chart-container"
                    style="height: 400px;">
                    <canvas class="tile-chart position-absolute top-0 start-0 w-100 h-100"
                        id="subproject-chart"></canvas>
                </div>
            </div>
        </div>
    </div>
    @if ($subprojectModal)
        <div class="modal fade show" id="helloModal" tabindex="-1" aria-modal="true" role="dialog"
            style="display: block;">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Greetings</h5>
                        <button type="button" class="btn-close" wire:click='$set("subprojectModal", false)'
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if (count($tableData) > 0)
                            <table class="table table-striped" id="subprojectTable">
                                <thead>
                                    <tr>
                                        <th>Project Name</th>
                                        <th>Proponent</th>
                                        <th>Cluster</th>
                                        <th>Region</th>
                                        <th>Province</th>
                                        <th>City/Municipality</th>
                                        <th>Subproject Confirmed</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($tableData as $data)
                                        <tr>
                                            <td>{{ $data['project_name'] }}</td>
                                            <td>{{ $data['proponent'] }}</td>
                                            <td>{{ $data['cluster'] }}</td>
                                            <td>{{ $data['region'] }}</td>
                                            <td>{{ $data['province'] }}</td>
                                            <td>{{ $data['city_municipality'] }}</td>
                                            <td>{{ $data['subproject_confirmed'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p>No data found.</p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary"
                            wire:click='$set("subprojectModal", false)'>Close</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-backdrop show"></div>
    @endif
</div>

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

            window.chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: groupKeys.map(key => chartData[key].title),
                    datasets: [{
                            label: 'No. of Subprojects',
                            backgroundColor: '#0066FF',
                            data: groupKeys.map(key => chartData[key].subject_count)
                        },
                        {
                            label: 'No. of Subprojects Beyond Timeline',
                            backgroundColor: '#3EA9E5',
                            data: groupKeys.map(key => chartData[key].beyond_timeline_count)
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
                                weight: 'bold',
                                size: 12
                            },
                            align: 'end',
                            anchor: 'end',
                            formatter: value => value > 0 ? value : ''
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

                }
            }, 50); // 50ms delay ensures canvas exists
            $wire.set('loader', false);
        });
    </script>
@endscript
