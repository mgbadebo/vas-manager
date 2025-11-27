<section aria-label="Admin quick actions">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
        @php
            $cards = [
                [
                    'title' => 'Services',
                    'icon' => 'bi-box-seam',
                    'color' => 'primary',
                    'count' => $services->count(),
                    'description' => 'Define the offerings that generate VAS revenue.',
                    'route' => route('admin.settings.sections.services'),
                ],
                [
                    'title' => 'Service Types',
                    'icon' => 'bi-tags',
                    'color' => 'info',
                    'count' => $serviceTypes->count(),
                    'description' => 'Categories for classifying services (e.g. Lottery, Games, Casino).',
                    'route' => route('admin.settings.sections.service-types'),
                ],
                [
                    'title' => 'MNOs',
                    'icon' => 'bi-phone',
                    'color' => 'success',
                    'count' => $mnos->count(),
                    'description' => 'Manage the mobile network operators we integrate with.',
                    'route' => route('admin.settings.sections.mnos'),
                ],
                [
                    'title' => 'Aggregators',
                    'icon' => 'bi-diagram-3',
                    'color' => 'warning',
                    'count' => $aggregators->count(),
                    'description' => 'Maintain the distribution partners for each service.',
                    'route' => route('admin.settings.sections.aggregators'),
                ],
                [
                    'title' => 'Banks',
                    'icon' => 'bi-bank',
                    'color' => 'primary',
                    'count' => $banks->count(),
                    'description' => 'Capture the bank accounts that receive payments.',
                    'route' => route('admin.settings.sections.banks'),
                ],
                [
                    'title' => 'Mandatory Expense Types',
                    'icon' => 'bi-exclamation-triangle',
                    'color' => 'danger',
                    'count' => $mandatoryTypes->count(),
                    'description' => 'Configure tax or regulatory deductions applied before revenue share.',
                    'route' => route('admin.settings.sections.mandatory-types'),
                ],
                [
                    'title' => 'Operational Categories',
                    'icon' => 'bi-folder',
                    'color' => 'info',
                    'count' => $operationalCategories->count(),
                    'description' => 'Group operational costs for easier allocation.',
                    'route' => route('admin.settings.sections.operational-categories'),
                ],
                [
                    'title' => 'Recipients',
                    'icon' => 'bi-people',
                    'color' => 'success',
                    'count' => $expenseRecipients->count(),
                    'description' => 'List of vendors / stakeholders receiving operational payouts.',
                    'route' => route('admin.settings.sections.recipients'),
                ],
            ];
        @endphp

        @foreach($cards as $card)
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <div class="bg-{{ $card['color'] }}-subtle rounded p-2">
                                    <i class="bi {{ $card['icon'] }} text-{{ $card['color'] }} fs-5"></i>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0 fw-semibold">{{ $card['title'] }}</h5>
                                    <small class="text-muted">{{ $card['count'] }} {{ \Illuminate\Support\Str::plural('record', $card['count']) }}</small>
                                </div>
                            </div>
                        </div>
                        <p class="text-muted small flex-grow-1 mb-3">{{ $card['description'] }}</p>
                        <a class="btn btn-{{ $card['color'] }} w-100" href="{{ $card['route'] }}">
                            <i class="bi bi-gear"></i> Manage {{ $card['title'] }}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>


