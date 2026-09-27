<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Change Password | EIMS</title>

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
                        Change Your Password
                    </p>
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="alert alert-warning">
                    For security, you must change your temporary password before continuing.
                </div>

                <form method="POST" action="{{ route('password.change.update') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="current_password" class="form-label">
                            Current Password
                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="current_password"
                            name="current_password"
                            required
                        >
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">
                            New Password
                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="password"
                            name="password"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">
                            Confirm New Password
                        </label>

                        <input
                            type="password"
                            class="form-control"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-dark w-100">
                        Change Password
                    </button>
                </form>

            </div>
        </div>

    </div>

</body>

</html>