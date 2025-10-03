<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Pemantauan Bahan Baku MBG</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 15px 0;
            margin-bottom: 20px;
        }

        .header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 24px;
        }

        .header .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Navigation */
        .nav {
            background-color: #34495e;
            padding: 10px 0;
            margin-bottom: 20px;
        }

        .nav ul {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .nav a {
            color: white;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 5px;
        }

        .nav a:hover,
        .nav a.active {
            background-color: #3498db;
        }

        /* Cards */
        .card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        /* Stats */
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            font-size: 28px;
            color: #3498db;
            margin-bottom: 5px;
        }

        .stat-card p {
            color: #666;
        }

        /* Tables */
        .table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .table tr:hover {
            background-color: #f5f5f5;
        }

        /* Forms */
        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-control:focus {
            border-color: #3498db;
            outline: none;
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
        }

        .btn-success {
            background-color: #27ae60;
            color: white;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn-warning {
            background-color: #f39c12;
            color: white;
        }

        .btn-secondary {
            background-color: #95a5a6;
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        /* Action column styling */
        .table td:last-child {
            width: 140px;
            min-width: 140px;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        .action-buttons .btn {
            white-space: nowrap;
        }

        /* Alerts */
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background-color: #cce7ff;
            color: #004085;
            border: 1px solid #b8daff;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            padding: 12px 16px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 8px;
        }

        .alert-error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
            padding: 12px 16px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 8px;
        }

        /* Status badges */
        .badge {
            padding: 5px 10px;
            border-radius: 15px;
            color: white;
            font-size: 12px;
        }

        .badge-success {
            background-color: #27ae60;
        }

        .badge-danger {
            background-color: #e74c3c;
        }

        .badge-warning {
            background-color: #f39c12;
        }

        .badge-info {
            background-color: #3498db;
        }

        .badge-secondary {
            background-color: #95a5a6;
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="container">
            <h1>Pemantauan Bahan Baku MBG</h1>
            <div class="user-info">
                <span>{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
                <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Logout</button>
                </form>
            </div>
        </div>
    </header>

    @if(auth()->user()->role === 'gudang')
    <nav class="nav">
        <div class="container">
            <ul>
                <li><a href="{{ route('gudang.dashboard') }}" class="{{ request()->routeIs('gudang.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('gudang.bahan-baku.index') }}" class="{{ request()->routeIs('gudang.bahan-baku.*') ? 'active' : '' }}">Bahan Baku</a></li>
                <li><a href="{{ route('gudang.permintaan.status') }}" class="{{ request()->routeIs('gudang.permintaan.*') ? 'active' : '' }}">Status Permintaan</a></li>
            </ul>
        </div>
    </nav>
    @elseif(auth()->user()->role === 'dapur')
    <nav class="nav">
        <div class="container">
            <ul>
                <li><a href="{{ route('dapur.dashboard') }}" class="{{ request()->routeIs('dapur.dashboard') ? 'active' : '' }}">Dashboard</a></li>
                <li><a href="{{ route('dapur.bahan-baku.index') }}" class="{{ request()->routeIs('dapur.bahan-baku.*') ? 'active' : '' }}">Lihat Bahan Baku</a></li>
                <li><a href="{{ route('dapur.permintaan.index') }}" class="{{ request()->routeIs('dapur.permintaan.*') ? 'active' : '' }}">Permintaan Bahan</a></li>
            </ul>
        </div>
    </nav>
    @endif

    <main class="container">
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @yield('content')
    </main>
</body>

</html>