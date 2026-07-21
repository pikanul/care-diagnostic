@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    @php
        $chartData = $visitorStats['chartData'] ?? ['daily' => [], 'hourly' => [], 'devices' => [], 'sources' => []];
    @endphp

    <style>
        .metric-card h2 {
            margin: 8px 0 0;
            font-size: 30px;
        }

        .dashboard-charts {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            gap: 16px;
            margin-top: 16px;
        }

        .chart-panel {
            min-width: 0;
            display: grid;
            gap: 14px;
        }

        .chart-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
        }

        .chart-head h2 {
            margin: 0;
            font-size: 20px;
        }

        .chart-tabs {
            display: inline-flex;
            gap: 6px;
            padding: 4px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: #f7f9fc;
        }

        .chart-tab {
            border: 0;
            border-radius: 6px;
            padding: 7px 10px;
            background: transparent;
            color: var(--muted);
            font-size: 13px;
        }

        .chart-tab.is-active {
            background: var(--accent);
            color: #fff;
        }

        .bar-chart {
            height: 280px;
            display: grid;
            grid-template-columns: repeat(var(--chart-count), minmax(0, 1fr));
            gap: 8px;
            align-items: end;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 8px;
            background: linear-gradient(180deg, #fbfdff, #f5f8fc);
        }

        .bar-item {
            min-width: 0;
            display: grid;
            gap: 8px;
            align-items: end;
            height: 100%;
            padding: 0;
            border: 0;
            background: transparent;
            color: inherit;
            cursor: pointer;
        }

        .bar-fill {
            min-height: 4px;
            border-radius: 7px 7px 3px 3px;
            background: linear-gradient(180deg, #0f766e, #14b8a6);
            box-shadow: 0 8px 18px rgba(15, 118, 110, .18);
            transition: height .24s ease, filter .18s ease, transform .18s ease;
        }

        .bar-item:hover .bar-fill,
        .bar-item:focus-within .bar-fill {
            filter: saturate(1.2);
            transform: translateY(-2px);
        }

        .bar-label {
            color: var(--muted);
            font-size: 11px;
            text-align: center;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .chart-tooltip {
            min-height: 22px;
            color: var(--text);
            font-size: 14px;
            font-weight: 700;
        }

        .donut-wrap {
            display: grid;
            grid-template-columns: 160px minmax(0, 1fr);
            gap: 18px;
            align-items: center;
        }

        .donut {
            width: 160px;
            height: 160px;
            border-radius: 999px;
            background: conic-gradient(var(--donut-colors, #0f766e 0 100%));
            display: grid;
            place-items: center;
            box-shadow: inset 0 0 0 22px #fff, 0 10px 26px rgba(16, 24, 40, .08);
        }

        .donut strong {
            font-size: 22px;
            color: var(--text);
        }

        .legend-list {
            display: grid;
            gap: 9px;
        }

        .legend-item,
        .source-bar {
            border: 1px solid var(--line);
            border-radius: 8px;
            padding: 10px;
            background: #fff;
        }

        .legend-item {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
        }

        .legend-name {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            min-width: 0;
        }

        .legend-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--dot-color);
            flex: 0 0 auto;
        }

        .source-bars {
            display: grid;
            gap: 10px;
        }

        .source-meta {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            font-size: 13px;
            font-weight: 700;
        }

        .source-track {
            height: 8px;
            margin-top: 8px;
            overflow: hidden;
            border-radius: 999px;
            background: #e9eef6;
        }

        .source-fill {
            height: 100%;
            width: var(--source-width);
            border-radius: inherit;
            background: linear-gradient(90deg, #0f766e, #14b8a6);
            transition: width .24s ease;
        }

        @media (max-width: 1100px) {
            .dashboard-charts,
            .donut-wrap {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 560px) {
            .bar-chart {
                gap: 5px;
                padding: 10px;
            }

            .chart-head {
                align-items: flex-start;
                flex-direction: column;
            }
        }
    </style>

    <section class="grid">
        <article class="panel metric-card">
            <div class="muted">Admin users</div>
            <h2>{{ $userCount }}</h2>
        </article>

        <article class="panel metric-card">
            <div class="muted">Two-factor structure</div>
            <h2>{{ auth()->user()->two_factor_enabled ? 'Enabled' : 'Ready' }}</h2>
        </article>

        <article class="panel metric-card">
            <div class="muted">Current account</div>
            <h2>{{ auth()->user()->is_active ? 'Active' : 'Inactive' }}</h2>
        </article>

        <article class="panel metric-card">
            <div class="muted">Total visitors</div>
            <h2>{{ number_format($visitorStats['total']) }}</h2>
        </article>

        <article class="panel metric-card">
            <div class="muted">Today visitors</div>
            <h2>{{ number_format($visitorStats['today']) }}</h2>
        </article>

        <article class="panel metric-card">
            <div class="muted">Real-time visitors</div>
            <h2>{{ number_format($visitorStats['active']) }}</h2>
        </article>
    </section>

    <section class="dashboard-charts">
        <article class="panel chart-panel">
            <div class="chart-head">
                <div>
                    <h2>Visitor trend</h2>
                    <div class="muted">Hover bars to inspect traffic.</div>
                </div>
                <div class="chart-tabs" aria-label="Visitor chart range">
                    <button type="button" class="chart-tab is-active" data-chart-tab="daily">7 days</button>
                    <button type="button" class="chart-tab" data-chart-tab="hourly">Today</button>
                </div>
            </div>
            <div class="chart-tooltip" data-chart-tooltip>Move over the graph</div>
            <div class="bar-chart" data-bar-chart style="--chart-count: 7;"></div>
        </article>

        <article class="panel chart-panel">
            <div class="chart-head">
                <div>
                    <h2>Devices</h2>
                    <div class="muted">Visitor device split.</div>
                </div>
            </div>
            <div class="donut-wrap">
                <div class="donut" data-donut-chart><strong>{{ number_format($visitorStats['total']) }}</strong></div>
                <div class="legend-list" data-donut-legend></div>
            </div>
        </article>

        <article class="panel chart-panel">
            <div class="chart-head">
                <div>
                    <h2>Traffic sources</h2>
                    <div class="muted">Campaign and referral performance.</div>
                </div>
            </div>
            <div class="source-bars" data-source-bars></div>
        </article>

        <article class="panel">
            <h2>Top pages</h2>
            <div class="table-list">
                @forelse ($visitorStats['topPages'] as $page)
                    <div class="table-row">
                        <div>{{ $page->path }}</div>
                        <div class="muted">{{ number_format($page->views) }} views</div>
                        <div></div>
                    </div>
                @empty
                    <p class="muted">No visitor data yet.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="dashboard-charts">
        <article class="panel">
            <h2>Core Web Vitals</h2>
            <p class="muted">Average browser performance samples from the last 7 days.</p>
            <div class="table-list">
                @forelse ($visitorStats['webVitals'] as $metric)
                    <div class="table-row">
                        <div><strong>{{ $metric->metric }}</strong></div>
                        <div>{{ number_format((float) $metric->average_value, 2) }}</div>
                        <div class="muted">{{ number_format($metric->samples) }} samples</div>
                    </div>
                @empty
                    <p class="muted">No Core Web Vitals data yet.</p>
                @endforelse
            </div>
        </article>

        <article class="panel">
            <h2>404 / Broken Links</h2>
            <p class="muted">Most frequent missing URLs captured from visitors.</p>
            <div class="table-list">
                @forelse ($visitorStats['notFoundErrors'] as $error)
                    <div class="table-row">
                        <div>{{ $error->path }}</div>
                        <div class="muted">{{ number_format($error->hits) }} hits</div>
                        <div class="muted">{{ \Illuminate\Support\Carbon::parse($error->last_seen)->diffForHumans() }}</div>
                    </div>
                @empty
                    <p class="muted">No broken links recorded yet.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="panel" style="margin-top: 16px;">
        <h2>Recent visitors</h2>

        <div class="table-list">
            @forelse ($visitorStats['recentVisitors'] as $visitor)
                <div class="table-row">
                    <div>{{ $visitor->created_at?->format('Y-m-d H:i') }}</div>
                    <div>
                        {{ $visitor->path }}
                        <div class="muted">
                            {{ $visitor->device_type }} · {{ $visitor->browser }} · {{ $visitor->platform }}
                            @if ($visitor->city || $visitor->country_code)
                                · {{ collect([$visitor->city, $visitor->region, $visitor->country_code])->filter()->join(', ') }}
                            @endif
                        </div>
                    </div>
                    <div class="muted">{{ $visitor->ip_address ?? 'Unknown IP' }}</div>
                </div>
            @empty
                <p class="muted">No recent visitor entries yet.</p>
            @endforelse
        </div>
    </section>

    <section class="panel" style="margin-top: 16px;">
        <h2>Recent audit activity</h2>

        <div class="table-list">
            @forelse ($recentAuditLogs as $log)
                <div class="table-row">
                    <div>{{ $log->created_at?->format('Y-m-d H:i') }}</div>
                    <div>{{ $log->event }}</div>
                    <div class="muted">{{ $log->user?->email ?? 'System' }}</div>
                </div>
            @empty
                <p class="muted">No audit entries yet.</p>
            @endforelse
        </div>
    </section>

    <script>
        (() => {
            const data = @json($chartData);
            const colors = ['#0f766e', '#0b66c3', '#12a75b', '#f59e0b', '#7c3aed', '#ef4444'];
            const barChart = document.querySelector('[data-bar-chart]');
            const tooltip = document.querySelector('[data-chart-tooltip]');
            const tabs = document.querySelectorAll('[data-chart-tab]');
            const donut = document.querySelector('[data-donut-chart]');
            const legend = document.querySelector('[data-donut-legend]');
            const sourceBars = document.querySelector('[data-source-bars]');

            const formatNumber = (value) => new Intl.NumberFormat().format(value || 0);
            const escapeHtml = (value) => String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');

            const renderBars = (type) => {
                const rows = data[type] || [];
                const max = Math.max(1, ...rows.map((row) => row.value || 0));

                if (!barChart) {
                    return;
                }

                barChart.style.setProperty('--chart-count', rows.length || 1);
                barChart.innerHTML = rows.map((row) => {
                    const height = Math.max(4, Math.round(((row.value || 0) / max) * 100));
                    const title = `${row.label}: ${formatNumber(row.value)} visits`;
                    const label = escapeHtml(row.label);

                    return `
                        <button class="bar-item" type="button" data-label="${label}" data-value="${row.value || 0}" title="${escapeHtml(title)}">
                            <span class="bar-fill" style="height:${height}%"></span>
                            <span class="bar-label">${label}</span>
                        </button>
                    `;
                }).join('') || '<p class="muted">No visitor data yet.</p>';

                barChart.querySelectorAll('.bar-item').forEach((item) => {
                    item.addEventListener('mouseenter', () => {
                        tooltip.textContent = `${item.dataset.label}: ${formatNumber(Number(item.dataset.value))} visits`;
                    });
                    item.addEventListener('focus', () => {
                        tooltip.textContent = `${item.dataset.label}: ${formatNumber(Number(item.dataset.value))} visits`;
                    });
                    item.addEventListener('click', () => {
                        tooltip.textContent = `${item.dataset.label}: ${formatNumber(Number(item.dataset.value))} visits`;
                    });
                });
            };

            const renderDonut = () => {
                const rows = data.devices || [];
                const total = rows.reduce((sum, row) => sum + (row.value || 0), 0);
                let start = 0;

                if (!donut || !legend) {
                    return;
                }

                if (!total) {
                    donut.style.setProperty('--donut-colors', '#d9e1ec 0 100%');
                    legend.innerHTML = '<p class="muted">No device data yet.</p>';
                    return;
                }

                const segments = rows.map((row, index) => {
                    const percent = ((row.value || 0) / total) * 100;
                    const end = start + percent;
                    const segment = `${colors[index % colors.length]} ${start}% ${end}%`;
                    start = end;
                    return segment;
                });

                donut.style.setProperty('--donut-colors', segments.join(', '));
                legend.innerHTML = rows.map((row, index) => `
                    <div class="legend-item">
                        <span class="legend-name"><span class="legend-dot" style="--dot-color:${colors[index % colors.length]}"></span>${escapeHtml(row.label)}</span>
                        <strong>${formatNumber(row.value)}</strong>
                    </div>
                `).join('');
            };

            const renderSources = () => {
                const rows = data.sources || [];
                const max = Math.max(1, ...rows.map((row) => row.value || 0));

                if (!sourceBars) {
                    return;
                }

                sourceBars.innerHTML = rows.map((row) => {
                    const width = Math.max(4, Math.round(((row.value || 0) / max) * 100));
                    const label = escapeHtml(row.label);

                    return `
                        <div class="source-bar" title="${label}: ${formatNumber(row.value)} visits">
                            <div class="source-meta">
                                <span>${label}</span>
                                <span>${formatNumber(row.value)}</span>
                            </div>
                            <div class="source-track"><div class="source-fill" style="--source-width:${width}%"></div></div>
                        </div>
                    `;
                }).join('') || '<p class="muted">No source data yet.</p>';
            };

            tabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    tabs.forEach((item) => item.classList.toggle('is-active', item === tab));
                    renderBars(tab.dataset.chartTab);
                });
            });

            renderBars('daily');
            renderDonut();
            renderSources();
        })();
    </script>
@endsection
