<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Pemantauan Bahan Baku MBG</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: bold;
        }
        
        .main-container {
            margin-top: 20px;
            margin-bottom: 40px;
        }
        
        /* Custom card styling */
        .custom-card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
        }
        
        /* Table improvements */
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-top: none;
        }
        
        /* Status badges custom colors */
        .badge-tersedia { background-color: #28a745; }
        .badge-hampir-habis { background-color: #ffc107; color: #000; }
        .badge-habis { background-color: #dc3545; }
        .badge-kadaluarsa { background-color: #6c757d; }

        /* Cards */
        .card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        /* Custom enhancements for Bootstrap */
        .navbar-brand {
            font-weight: bold;
        }
        
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
        }
        
        .stat-card {
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
        }
        
        .table-responsive {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .action-buttons {
            white-space: nowrap;
        }
    </style>
</head>

<body>
    <!-- Bootstrap Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-building"></i> Pemantauan Bahan Baku MBG
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @if(auth()->user()->role === 'gudang')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('gudang.dashboard') ? 'active' : '' }}" href="{{ route('gudang.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('gudang.bahan-baku.*') ? 'active' : '' }}" href="{{ route('gudang.bahan-baku.index') }}">
                                <i class="bi bi-box-seam"></i> Bahan Baku
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('gudang.permintaan.*') ? 'active' : '' }}" href="{{ route('gudang.permintaan.status') }}">
                                <i class="bi bi-clipboard-check"></i> Status Permintaan
                            </a>
                        </li>
                    @elseif(auth()->user()->role === 'dapur')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dapur.dashboard') ? 'active' : '' }}" href="{{ route('dapur.dashboard') }}">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dapur.bahan-baku.*') ? 'active' : '' }}" href="{{ route('dapur.bahan-baku.index') }}">
                                <i class="bi bi-eye"></i> Lihat Bahan Baku
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dapur.permintaan.*') ? 'active' : '' }}" href="{{ route('dapur.permintaan.index') }}">
                                <i class="bi bi-plus-circle"></i> Permintaan Bahan
                            </a>
                        </li>
                    @endif
                </ul>
                
                <div class="navbar-text me-3">
                    <i class="bi bi-person-circle"></i> {{ auth()->user()->name }} 
                    <span class="badge bg-light text-dark">{{ ucfirst(auth()->user()->role) }}</span>
                </div>
                
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container-fluid py-4">
        <!-- Success Message -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Error Messages -->
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Terdapat kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <!-- Page Content -->
        @yield('content')
    </main>
</body>

</html>