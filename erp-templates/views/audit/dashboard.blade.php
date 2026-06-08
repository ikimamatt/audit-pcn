{{--
    Contoh blade view ERP untuk halaman Dashboard Audit.
    File ini ditempatkan di: resources/views/audit/dashboard.blade.php (di repo ERP)

    $headers dikirim oleh AuditController dan berisi:
    - X-ERP-Payload (Base64-encoded JSON)
    - X-ERP-Signature (HMAC-SHA256)
    - X-ERP-Domain (domain ERP)
--}}

@extends('layouts.app')

@section('title', 'Dashboard Audit SPI')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <h1>Dashboard Audit SPI</h1>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            {{-- Loading State --}}
            <div id="loading-state" class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Memuat data...</span>
                </div>
                <p class="mt-2 text-muted">Memuat data dashboard audit...</p>
            </div>

            {{-- Dashboard Content (hidden until loaded) --}}
            <div id="dashboard-content" style="display: none;">
                {{-- Summary Cards --}}
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3 id="total-direncanakan">0</h3>
                                <p>Direncanakan</p>
                            </div>
                            <div class="icon"><i class="fas fa-calendar-alt"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3 id="total-terealisasi">0</h3>
                                <p>Terealisasi</p>
                            </div>
                            <div class="icon"><i class="fas fa-check-circle"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3 id="total-temuan">0</h3>
                                <p>Temuan</p>
                            </div>
                            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3 id="rekomendasi-open">0</h3>
                                <p>Rekomendasi Open</p>
                            </div>
                            <div class="icon"><i class="fas fa-clipboard-list"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Error State --}}
            <div id="error-state" style="display: none;" class="text-center py-5">
                <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                <p class="text-muted" id="error-message">Gagal memuat data dashboard.</p>
                <button class="btn btn-primary" onclick="loadDashboard()">Coba Lagi</button>
            </div>
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    // ERP Headers yang diinject oleh BasedController
    const erpHeaders = @json($headers);

    // Base URL Audit PCN Service
    const auditApiUrl = @json(config('service-api.audit.' . app()->environment() . '.url', 'http://127.0.0.1:8001/api/audit'));

    async function loadDashboard() {
        document.getElementById('loading-state').style.display = 'block';
        document.getElementById('dashboard-content').style.display = 'none';
        document.getElementById('error-state').style.display = 'none';

        try {
            const response = await axios.get(`${auditApiUrl}/dashboard/analitik`, {
                headers: {
                    'X-ERP-Payload':   erpHeaders['X-ERP-Payload'],
                    'X-ERP-Signature': erpHeaders['X-ERP-Signature'],
                    'X-ERP-Domain':    erpHeaders['X-ERP-Domain'],
                    'Accept':          'application/json',
                }
            });

            if (response.data.success) {
                const summary = response.data.data.summary;
                document.getElementById('total-direncanakan').textContent = summary.total_direncanakan;
                document.getElementById('total-terealisasi').textContent = summary.total_terealisasi;
                document.getElementById('total-temuan').textContent = summary.total_temuan;
                document.getElementById('rekomendasi-open').textContent = summary.rekomendasi_open;

                document.getElementById('loading-state').style.display = 'none';
                document.getElementById('dashboard-content').style.display = 'block';
            }
        } catch (error) {
            console.error('Failed to load dashboard:', error);
            document.getElementById('loading-state').style.display = 'none';
            document.getElementById('error-state').style.display = 'block';

            if (error.response) {
                document.getElementById('error-message').textContent =
                    error.response.data.message || 'Gagal memuat data dashboard.';
            }
        }
    }

    // Load on page ready
    document.addEventListener('DOMContentLoaded', loadDashboard);
</script>
@endsection
