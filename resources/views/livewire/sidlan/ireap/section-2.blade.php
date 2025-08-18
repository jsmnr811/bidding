<?php

use Illuminate\View\View;
use Livewire\Volt\Component;
use App\Services\SidlanAPIServices;

new class extends Component {
    public $chartData = [];

    public $pipelinePieChartData = [];
    public $approvedPieChartData = [];

    public function mount($irZeroOneData = []): void
    {
        $apiService = new SidlanAPIServices();
        $irZeroTwoData = $apiService->executeRequest(['dataset_id' => 'ir-01-002']);
        $irZeroThreeAData = $apiService->executeRequest(['dataset_id' => 'ir-01-003a']);
        $irZeroThreeBData = $apiService->executeRequest(['dataset_id' => 'ir-01-003b']);

        $totals = $this->computePipeline($irZeroOneData, $irZeroTwoData, $irZeroThreeAData, $irZeroThreeBData);

        $totalAllocation = 100;

        $pipeline = $totals['pipeline'];


        $this->pipelinePieChartData = $totals['pipeline_chart_data'];

        $this->chartData = [
            'labels' => ['Subproject Portfolio'],
            'datasets' => [
                [
                    'label' => 'Total Allocation',
                    'data' => [$totalAllocation],
                    'backgroundColor' => '#0066FF',
                    'stack' => 'Stack 0',
                ],
                [
                    'label' => 'Pipelined',
                    'data' => [$pipeline],
                    'backgroundColor' => '#3EA9E5',
                    'stack' => 'Stack 1',
                ],
            ],
        ];
    }

    private function computePipeline(array $zeroOneData, array $zeroTwoData, array $zeroThreeAData, array $zeroThreeBData): array
    {
        $zeroOne = collect($zeroOneData);
        $zeroTwo = collect($zeroTwoData);
        $zeroThreeA = collect($zeroThreeAData);
        $zeroThreeB = collect($zeroThreeBData);

        $pipelineItems = $zeroOne->filter(fn($item) => $item['stage'] === 'Pre-procurement' && $item['status'] === 'Subproject Confirmed');

        $pipeline = $pipelineItems->count();

        $statusOrder = ['No. of SPs', 'Luzon B', 'Visayas', 'Mindanao'];
        $clusterColors = [
            'Luzon A' => '#004EF5',
            'Luzon B' => '#1ABC9C',
            'Visayas' => '#3498DB',
            'Mindanao' => '#9B59B6',
        ];

        // Group costs by cluster for pipeline and approved
        $pipelineCostsRaw = $pipelineItems
            ->groupBy('cluster')
            ->map(
                fn($items) => $items->reduce(function ($carry, $item) {
                    $cost = $item['cost_during_validation'] ?: $item['sp_indicative_cost'];
                    return $carry + floatval($cost);
                }, 0.0),
            )
            ->toArray();


        $pipelineCostsPerCluster = [];

        foreach ($clusterOrder as $cluster) {
            $pipelineCostsPerCluster[$cluster] = $pipelineCostsRaw[$cluster] ?? 0.0;
        }

        $pipelineChartData = [];
        $approvedChartData = [];

        foreach ($clusterOrder as $cluster) {
            $pipelineChartData[] = [
                'label' => $cluster,
                'data' => $pipelineCostsPerCluster[$cluster],
                'backgroundColor' => $clusterColors[$cluster],
            ];
        }

        return [
            'pipeline' => $pipeline,
            'pipeline_costs_per_cluster' => $pipelineCostsPerCluster, // simple array cluster => cost
            'pipeline_chart_data' => $pipelineChartData, // data ready for charts
        ];
    }

    public function placeholder(): View
    {
        return view('livewire.sidlan.ireap.placeholder.section-1');
    }
};

?>


<div class="row row-cols-1 row-gap-4 mt-4 d-nones">
    <div class="col">
        <div class="tile-container h-100 d-flex flex-column">
            <div class="tile-title d-flex flex-column flex-lg-row row-gap-2 justify-content-between align-items-start"
                style="font-size: 1.2rem;">
                <span>
                    I-REAP Subprojects Currently in the Pipeline (Number of Subprojects by Status)
                </span>
                <div class="d-flex flex-row gap-2 align-items-center small">
                    <div class="fw-normal">Show:</div>
                    <select name="" id="cbo-filter-pipeline-status"
                        class="form-select filter-dropdown pe-lg-5">
                        <option value="All">All</option>
                        <optgroup label="Clusterwide">
                            <option value="Luzon A">Luzon A</option>
                            <option value="Luzon B">Luzon B</option>
                            <option value="Visayas">Visayas</option>
                            <option value="Mindanao">Mindanao</option>
                        </optgroup>
                        <optgroup label="Regionwide">
                            <option value="Cordillera Administrative Region (CAR)" data-group="region">CAR
                            </option>
                            <option value="Ilocos Region (Region I)" data-group="region">Region 01</option>
                            <option value="Cagayan Valley (Region II)" data-group="region">Region 02</option>
                            <option value="Central Luzon (Region III)" data-group="region">Region 03</option>
                            <option value="CALABARZON (Region IV-A)" data-group="region">Region 04A</option>
                            <option value="MIMAROPA (Region IV-B)" data-group="region">Region 04B</option>
                            <option value="Bicol Region (Region V)" data-group="region">Region 05</option>
                            <option value="Western Visayas (Region VI)" data-group="region">Region 06</option>
                            <option value="Central Visayas (Region VII)" data-group="region">Region 07
                            </option>
                            <option value="Eastern Visayas (Region VIII)" data-group="region">Region 08
                            </option>
                            <option value="Zamboanga Peninsula (Region IX)" data-group="region">Region 09
                            </option>
                            <option value="Northern Mindanao (Region X)" data-group="region">Region 10
                            </option>
                            <option value="Davao Region (Region XI)" data-group="region">Region 11</option>
                            <option value="SOCCSKSARGEN (Region XII)" data-group="region">Region 12</option>
                            <option value="Caraga (Region XIII)" data-group="region">Region 13</option>
                            <option value="Bangsamoro Autonomous Region of Muslim Mindanao (BARMM)"
                                data-group="region">BARMM</option>
                        </optgroup>
                    </select>
                </div>
            </div>
            <div class="tile-content position-relative overflow-hidden flex-grow-1 chart-container"
                style="height: 400px;">
                <canvas id="chrt-pipelined-by-status"
                    class="custom-chart position-absolute top-0 start-0 bottom-0 end-0" width="684"
                    height="600"
                    style="display: block; box-sizing: border-box; height: 300px; width: 342px;"></canvas>
            </div>
        </div>
    </div>
    <!-- end of pipeline status chart -->
</div>
@script
<script>
    let approvedChart = null;
    window.ChartThree = function() {
        let chartInstance = null; // Track the chart instance

        return {
            init() {
               
            }
        };
    }
 </script>
@endscript