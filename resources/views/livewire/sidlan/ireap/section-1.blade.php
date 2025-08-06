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

        $totals = $this->computePipelineAndApproved($irZeroOneData, $irZeroTwoData);

        $totalAllocation = 100; //Static value - change to correct value

        $pipeline = $totals['pipeline'];

        $approved = $totals['approved'];

        $this->pipelinePieChartData = $totals['pipeline_chart_data'];

        $this->approvedPieChartData = $totals['approved_chart_data'];
        $this->chartData = [
            'labels' => ['Chart One'],
            'datasets' => [
                [
                    'label' => 'Total Allocation',
                    'data' => [$totalAllocation],
                    'backgroundColor' => '#0066FF',
                    'stack' => 'Stack 0',
                ],
                [
                    'label' => 'Approved',
                    'data' => [$approved],
                    'backgroundColor' => '#2AB7A9',
                    'stack' => 'Stack 1',
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

    private function computePipelineAndApproved(array $zeroOneData, array $zeroTwoData): array
    {
        $zeroOne = collect($zeroOneData);
        $zeroTwo = collect($zeroTwoData);

        $nol1Lookup = $zeroTwo->mapWithKeys(fn($item) => [$item['sp_id'] => $item['nol1_issued']]);

        $pipelineItems = $zeroOne->filter(fn($item) => $item['stage'] === 'Pre-procurement' && $item['status'] === 'Subproject Confirmed');
        $approvedItems = $zeroOne->filter(fn($item) => in_array($item['stage'], ['Implementation', 'Procurement', 'Completed']) && !empty($nol1Lookup[$item['sp_id']] ?? null));

        $pipeline = $pipelineItems->count();
        $approved = $approvedItems->count();

        $clusterOrder = ['Luzon A', 'Luzon B', 'Visayas', 'Mindanao'];
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

        $approvedCostsRaw = $approvedItems
            ->groupBy('cluster')
            ->map(
                fn($items) => $items->reduce(function ($carry, $item) {
                    $cost = $item['cost_during_validation'] ?: $item['sp_indicative_cost'];
                    return $carry + floatval($cost);
                }, 0.0),
            )
            ->toArray();

        $pipelineCostsPerCluster = [];
        $approvedCostsPerCluster = [];

        foreach ($clusterOrder as $cluster) {
            $pipelineCostsPerCluster[$cluster] = $pipelineCostsRaw[$cluster] ?? 0.0;
            $approvedCostsPerCluster[$cluster] = $approvedCostsRaw[$cluster] ?? 0.0;
        }

        // Prepare data structures including colors for charts (optional)
        // For example, if you want to return array of arrays for chart datasets:

        $pipelineChartData = [];
        $approvedChartData = [];

        foreach ($clusterOrder as $cluster) {
            $pipelineChartData[] = [
                'label' => $cluster,
                'data' => $pipelineCostsPerCluster[$cluster],
                'backgroundColor' => $clusterColors[$cluster],
            ];
            $approvedChartData[] = [
                'label' => $cluster,
                'data' => $approvedCostsPerCluster[$cluster],
                'backgroundColor' => $clusterColors[$cluster],
            ];
        }

        return [
            'pipeline' => $pipeline,
            'approved' => $approved,
            'pipeline_costs_per_cluster' => $pipelineCostsPerCluster, // simple array cluster => cost
            'approved_costs_per_cluster' => $approvedCostsPerCluster, // simple array cluster => cost
            'pipeline_chart_data' => $pipelineChartData, // data ready for charts
            'approved_chart_data' => $approvedChartData, // data ready for charts
        ];
    }

    public function placeholder(): View
    {
        return view('livewire.sidlan.ireap.placeholder.section-1');
    }
};

?>
<div class="row row-cols-1 row-cols-lg-2 row-gap-4 mt-5">
    <div class="col">
        <div class="tile-container">
            <div class="tile-title ">Chart One</div>
            <div class="tile-content position-relative overflow-hidden chart-container" style="height: 400px;"
                x-data="ChartOne()" x-init="init()">
                <canvas class="tile-chart position-absolute top-0 start-0 w-100 h-100" id="chart-one"></canvas>
            </div>
            <div>
                <ul class="small mt-2">
                    <li>
                        Cost of approved SPs now constitutes <span id="approved-percentage"
                            class="text-primary">53%</span> of
                        the total allocation for financing
                        rural infrastructures (Php <span id="approved-amount" class="text-primary">18.15 B</span> out of
                        Php
                        <span id="total-budget" class="text-primary">34.28 B</span>)
                    </li>
                    <li>
                        Cost of pipelined SPs is <span id="pipeline-budget-stat" class="text-danger">above</span> the
                        remainng
                        fund (unallocated) for financing rural
                        infrastructures, by <span id="pipeline-excess">39.8</span>% (Php <span id="excess-amount">6.42
                            B</span>)
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="row row-cols-1 row-gap-4 h-100">
            <div class="row">
                <div class="tile-container h-100 d-flex flex-column">
                    <div class="tile-title" style="font-size: 1.2rem;">Cost of Pipelined Subprojects by Cluster</div>
                    <div class="tile-content position-relative overflow-hidden flex-grow-1 chart-container"
                        x-data="PipelinePieChart()" x-init="init()">
                        <canvas id="chart-cluster-pipeline"
                            class="position-absolute top-0 start-0 bottom-0 end-0"></canvas>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="tile-container h-100 d-flex flex-column">
                    <div class="tile-title" style="font-size: 1.2rem;">Cost of Approved Subprojects by Cluster</div>
                    <div class="tile-content position-relative overflow-hidden flex-grow-1 chart-container"
                        x-data="ApprovedPieChart()" x-init="init()">
                        <canvas id="chart-cluster-approved"
                            class="position-absolute top-0 start-0 bottom-0 end-0"></canvas>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@script
    <script>
        let approvedChart = null;
        window.ChartOne = function() {
            let chartInstance = null; // Track the chart instance

            return {
                init() {
                    const canvas = document.getElementById('chart-one');
                    const ctx = canvas.getContext('2d');

                    if (chartInstance !== null) {
                        chartInstance.destroy();
                    }

                    const chartData = @json($chartData);

                    chartInstance = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: chartData.labels,
                            datasets: chartData.datasets.map(dataset => ({
                                ...dataset,
                                borderRadius: 12, // Rounded bar corners
                                barPercentage: 0.6, // Prevent bars from stretching full width
                                categoryPercentage: 0.6, // Adds spacing between bars
                            }))
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            layout: {
                                padding: {
                                    top: 10,
                                    bottom: 10,
                                    left: 10,
                                    right: 10
                                }
                            },
                            scales: {
                                x: {
                                    stacked: true,
                                    grid: {
                                        display: false
                                    },
                                    ticks: {
                                        font: {
                                            size: 12
                                        }
                                    }
                                },
                                y: {
                                    stacked: true,
                                    beginAtZero: true,
                                    ticks: {
                                        callback: value => value + 'B',
                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    labels: {
                                        color: '#000',
                                        font: {
                                            size: 13
                                        }
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: context => `${context.dataset.label}: ${context.parsed.y}B`
                                    }
                                },
                                datalabels: {
                                    display: true,
                                    color: 'white',
                                    font: {
                                        weight: 'bold',
                                        size: 12
                                    },
                                    formatter: value => value.toFixed(2)
                                }
                            }
                        },
                        plugins: [ChartDataLabels]
                    });
                }
            };
        }
        // Pipeline Pie Chart
        window.PipelinePieChart = function() {
            let pipelineChart = null;

            return {
                init() {
                    const rawData = @json($pipelinePieChartData);
                    const canvas = document.getElementById('chart-cluster-pipeline');
                    if (!canvas) return;

                    // Destroy if already exists
                    if (pipelineChart) {
                        pipelineChart.destroy();
                    }

                    const ctx = canvas.getContext('2d');
                    const data = {
                        labels: rawData.map(d => d.label),
                        datasets: [{
                            data: rawData.map(d => d.data),
                            backgroundColor: rawData.map(d => d.backgroundColor)
                        }]
                    };

                    pipelineChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        font: {
                                            size: 12
                                        },
                                        padding: 20 // <-- Increase space between chart and legend
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: ctx =>
                                            `${ctx.label}: ₱ ${ctx.parsed.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
                                    }
                                },
                                datalabels: {
                                    anchor: 'end',
                                    align: 'end',
                                    offset: 6,
                                    clip: false,
                                    color: (context) => {
                                        // Match label color with corresponding slice color
                                        return context.chart.data.datasets[0].backgroundColor[context
                                            .dataIndex];
                                    },
                                    font: {
                                        weight: 'bold',
                                        size: 12
                                    },
                                    formatter: value => `₱ ${(value / 1_000_000_000).toFixed(2)}B`
                                }
                            }
                        },
                        plugins: [ChartDataLabels]
                    });
                }
            }
        };

        // Approved Pie Chart
        window.ApprovedPieChart = function() {
            return {
                init() {
                    const rawData = @json($approvedPieChartData);

                    const canvas = document.getElementById('chart-cluster-approved');
                    if (!canvas) return;

                    // destroy previous chart
                    if (Chart.getChart(canvas)) {
                        Chart.getChart(canvas).destroy();
                    }

                    const ctx = canvas.getContext('2d');

                    const data = {
                        labels: rawData.map(d => d.label),
                        datasets: [{
                            data: rawData.map(d => parseFloat(d.data) || 0),
                            backgroundColor: rawData.map(d => d.backgroundColor)
                        }]
                    };

                    approvedChart = new Chart(ctx, {
                        type: 'doughnut',
                        data,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        font: {
                                            size: 12
                                        },
                                        padding: 20 // <-- Increase space between chart and legend
                                    }
                                },
                                tooltip: {
                                    callbacks: {
                                        label: ctx =>
                                            `${ctx.label}: ₱ ${ctx.parsed.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`
                                    }
                                },
                                datalabels: {
                                    anchor: 'end',
                                    align: 'end',
                                    offset: 6,
                                    clip: false,
                                    color: (context) => {
                                        // Match label color with corresponding slice color
                                        return context.chart.data.datasets[0].backgroundColor[context
                                            .dataIndex];
                                    },
                                    font: {
                                        weight: 'bold',
                                        size: 12
                                    },
                                    formatter: value => `₱ ${(value / 1_000_000_000).toFixed(2)}B`
                                }
                            }
                        },
                        plugins: [ChartDataLabels]
                    });
                }
            }
        }
    </script>
@endscript
