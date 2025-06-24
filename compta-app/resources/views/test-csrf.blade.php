<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test CSRF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3>Test CSRF Token</h3>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('test-csrf-post') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="test" class="form-label">Champ de test</label>
                                <input type="text" class="form-control" id="test" name="test" value="Test CSRF">
                            </div>
                            <button type="submit" class="btn btn-primary">Tester CSRF</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
