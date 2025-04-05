@php
    $services = config('services_status.services');
@endphp

    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Surveillance des services</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light text-dark">

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-9">
                <h1 class="mb-4">Statut des services</h1>
            </div>
            <div class="col-lg-3">
                <div class="border rounded p-4 bg-white shadow-sm">
                    <h5 class="text-success">SERVICE ACTIF DEPUIS</h5>
                    <p class="mb-1 text-success">{{ $serviceDate1st }}</p>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-hover table-striped align-middle">
            <thead class="table-dark">
            <tr>
                <th>Service</th>
                <th>Statut actuel</th>
                <th>Disponibilité (24 dernières heures)</th>
            </tr>
            </thead>
            <tbody>
            @foreach($currentStatuses as $name => $status)
                <tr>
                    <td>
                        @if (!empty($services[$name]))
                            <a href="{{ $services[$name]['url'] ?? '#' }}" target="_blank">{{ $name }}</a>
                        @else
                            {{ $name }}
                        @endif
                    </td>
                    <td>
                        @if($status === 'functional')
                            <span class="badge bg-success">Fonctionnel</span>
                        @elseif($status === 'problem-exist')
                            <span class="badge bg-warning text-dark">Problème détecté</span>
                        @else
                            <span class="badge bg-danger">Non fonctionnel</span>
                        @endif
                    </td>
                    <td>{{ $uptimes24h[$name] ?? 0 }}%</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <hr class="my-5">

        <h2 class="mb-4">Disponibilité globale</h2>
        <div class="row">
            <div class="col-md-6">
                <div class="card border-success mb-3">
                    <div class="card-body">
                        <h5 class="card-title">24 dernières heures</h5>
                        <p class="card-text fs-4 text-success">{{ $global24h }}%</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card border-primary mb-3">
                    <div class="card-body">
                        <h5 class="card-title">7 derniers jours</h5>
                        <p class="card-text fs-4 text-primary">{{ $global7d }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <hr class="my-5">

        <h2 class="mb-4 text-danger">Dernières interruptions (5)</h2>

        <table class="table table-bordered table-hover table-striped align-middle">
            <thead class="table-danger">
            <tr>
                <th>Service</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Durée</th>
                <th>Issues</th>
            </tr>
            </thead>
            <tbody>
            @forelse($latestFailures as $failure)
                <tr>
                    <td>{{ $failure->service_name }}</td>
                    <td>{{ $failure->started_at->format('Y-m-d H:i') }}</td>
                    <td>
                        {{ $failure->ended_at ? $failure->ended_at->format('Y-m-d H:i') : 'En cours...' }}
                    </td>
                    <td>{{ $failure->getDurationFormattedAttr() }}</td>
                    <td>
                        @if (!empty($failure->lastStatus?->issues))
                            <div class="bg-light p-3 rounded">
                                @foreach($failure->lastStatus->issues as $issue)
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="text-warning me-2">!!️</span>
                                        <small class="text-dark">{{ $issue }}</small>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Aucune interruption enregistrée.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
