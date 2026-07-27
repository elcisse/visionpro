<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion — {{ config('app.name') }}</title>

    <link rel="stylesheet" href="{{ asset('vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/adminlte/dist/css/adminlte.min.css') }}">
    <style>
        html, body { height: 100%; margin: 0; }
        .login-split { display: flex; min-height: 100vh; }
        .login-split-image {
            flex: 1 1 50%;
            background-image: url('{{ asset('img/login-machine.jpg') }}');
            background-position: center;
            background-size: cover;
            background-repeat: no-repeat;
            background-color: #f4f6f9;
        }
        .login-split-form {
            flex: 1 1 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .login-split-form .card { width: 100%; max-width: 380px; }
        @media (max-width: 767px) {
            .login-split-image { display: none; }
        }
    </style>
</head>
<body class="hold-transition">
    <div class="login-split">
        <div class="login-split-image"></div>

        <div class="login-split-form">
            <div>
                <div class="login-logo text-center mb-3">
                    <b>{{ config('app.name') }}</b>
                </div>

                <div class="card card-outline card-primary">
                    <div class="card-header text-center">
                        <h3 class="card-title">Connexion</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ url('login') }}" method="post">
                            @csrf

                            <div class="input-group mb-3">
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    value="{{ old('email') }}" placeholder="Email" autofocus>
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-envelope"></span></div>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="input-group mb-3">
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror" placeholder="Mot de passe">
                                <div class="input-group-append">
                                    <div class="input-group-text"><span class="fas fa-lock"></span></div>
                                </div>
                                @error('password')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-check mb-3">
                                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                                <label for="remember" class="form-check-label">Se souvenir de moi</label>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block" style="white-space: nowrap;">Se connecter</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
