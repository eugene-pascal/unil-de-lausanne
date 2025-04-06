@extends('layouts.default')

@section('title', 'Liste des statuts')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <h1 class="mb-4">Liste des statuts</h1>
        </div>
    </div>

    <div class="col-lg-3 text-lg-end mt-3 mt-lg-0" style="margin-bottom: 1em;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                <li class="breadcrumb-item active" aria-current="page">Statuts</li>
            </ol>
        </nav>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
            <tr>
                <th>Service</th>
                <th>Type</th>
                <th>Statut</th>
                <th>Problèmes</th>
                <th>Dernière vérification</th>
            </tr>
            </thead>
            <tbody>
            @forelse($statuses as $status)
                <tr>
                    <td>{{ $status->service_name }}</td>
                    <td>{{ $status->type }}</td>
                    <td>
                        <span class="badge bg-{{ $status->status === \App\Enums\ServiceStatusEnum::FUNCTIONAL->value ? 'success' : 'danger' }}">
                            {{ ucfirst($status->status) }}
                        </span>
                    </td>
                    <td>
                        @if(!empty($status->issues))
                            <ul class="mb-0">
                                @foreach($status->issues as $issue)
                                    <li><small>{{ $issue }}</small></li>
                                @endforeach
                            </ul>
                        @else
                            <small class="text-muted"></small>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($status->checked_at)->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Aucun statut trouvé.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $statuses->links('pagination::bootstrap-5') }}
    </div>
@endsection
