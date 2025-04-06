@extends('layouts.default')

@section('title', 'Liste des interruptions')

@section('content')
    <div class="row">
        <div class="col-lg-9">
            <h1 class="mb-4">Liste des interruptions</h1>
        </div>
    </div>

    <div class="col-lg-3 text-lg-end mt-3 mt-lg-0" style="margin-bottom: 1em;">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Accueil</a></li>
                <li class="breadcrumb-item active" aria-current="page">Interruptions</li>
            </ol>
        </nav>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
            <tr>
                <th>Service</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Durée</th>
            </tr>
            </thead>
            <tbody>
            @forelse($issues as $failure)
                <tr>
                    <td>{{ $failure->service_name }}</td>
                    <td>{{ $failure->started_at->format('Y-m-d H:i') }}</td>
                    <td>
                        {{ $failure->ended_at ? $failure->ended_at->format('Y-m-d H:i') : 'En cours...' }}
                    </td>
                    <td>{{ $failure->getDurationFormattedAttr() }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">Aucun interruption trouvé.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $issues->links('pagination::bootstrap-5') }}
    </div>
@endsection
