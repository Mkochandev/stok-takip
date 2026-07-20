@extends('layouts.app')

@section('title', 'Ustalar')
@section('page-title', '👷 Ustalar')

@section('header-actions')
    <a href="{{ route('ustalar.create') }}" class="btn btn-primary btn-sm">
        ➕ Usta Ekle
    </a>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
        <span class="card-title">Tüm Ustalar</span>
        <span class="badge badge-secondary">{{ $ustalar->total() }} usta</span>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Usta</th>
                    <th>Uzmanlık</th>
                    <th>Telefon</th>
                    <th>Günlük Ücret</th>
                    <th>Mesai Saati</th>
                    <th>Durum</th>
                    <th>İşlemler</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ustalar as $usta)
                <tr>
                    <td>
                        <div class="d-flex align-center gap-2">
                            <div class="avatar-circle">
                                {{ strtoupper(mb_substr($usta->ad, 0, 1)) }}{{ strtoupper(mb_substr($usta->soyad, 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-semibold">{{ $usta->ad_soyad }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="text-muted">{{ $usta->uzmanlik ?? '—' }}</td>
                    <td class="text-muted">{{ $usta->telefon ?? '—' }}</td>
                    <td class="text-success fw-semibold">{{ number_format($usta->gunluk_ucret, 0, ',', '.') }}₺</td>
                    <td class="text-muted">{{ number_format($usta->mesai_saatlik_ucret, 0, ',', '.') }}₺/s</td>
                    <td>
                        @if($usta->durum === 'aktif')
                            <span class="badge badge-success">● Aktif</span>
                        @else
                            <span class="badge badge-secondary">● Pasif</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <a href="{{ route('ustalar.show', $usta) }}" class="btn btn-secondary btn-sm">👁️</a>
                            <a href="{{ route('ustalar.edit', $usta) }}" class="btn btn-warning btn-sm">✏️</a>
                            <form action="{{ route('ustalar.destroy', $usta) }}" method="POST"
                                  onsubmit="return confirm('{{ $usta->ad_soyad }} silinsin mi?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-danger btn-sm">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted" style="padding:40px;">
                        Henüz usta eklenmemiş.
                        <a href="{{ route('ustalar.create') }}">İlk ustayı ekle →</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($ustalar->hasPages())
        <div class="pagination" style="margin-top:16px;">
            {{ $ustalar->links() }}
        </div>
    @endif
</div>

@endsection
