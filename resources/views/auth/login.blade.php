<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | EIMS</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body class="bg-light">

    <div class="container min-vh-100 d-flex align-items-center justify-content-center">

        <div class="card shadow-sm border-0" style="max-width: 420px; width: 100%;">
            <div class="card-body p-4 p-md-5">

                <div class="text-center mb-4">
                    <h3 class="fw-bold mb-1">EIMS</h3>
                    <p class="text-muted mb-0">
                        Enterprise Information Management System
                    </p>
                </div>

                <h5 class="mb-4">Sign In</h5>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="username" class="form-label">
                            Username
                        </label>

                        <input
                            type="text"
                            class="form-control @error('username') is-invalid @enderror"
                            id="username"
                            name="username"
                            value="{{ old('username') }}"
                            placeholder="Enter your username"
                            autocomplete="username"
                            required
                            autofocus
                        >

                        @error('username')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            Password
                        </label>

                        <input
                            type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >

                        @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="form-check mb-4">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="remember"
                            value="1"
                            id="remember"
                        >

                        <label class="form-check-label" for="remember">
                            Remember me
                        </label>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">
                        Sign In
                    </button>
                </form>

            </div>
        </div>

    </div>

</body>

</html>