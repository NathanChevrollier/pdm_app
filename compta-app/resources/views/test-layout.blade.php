@extends('layouts.app')

@section('title', 'Test du Layout')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card mb-4">
      <h5 class="card-header">Test des composants Sneat</h5>
      <div class="card-body">
        <h5>Boutons</h5>
        <div class="row mb-4">
          <div class="col">
            <button type="button" class="btn btn-primary">Primary</button>
            <button type="button" class="btn btn-secondary">Secondary</button>
            <button type="button" class="btn btn-success">Success</button>
            <button type="button" class="btn btn-danger">Danger</button>
            <button type="button" class="btn btn-warning">Warning</button>
            <button type="button" class="btn btn-info">Info</button>
            <button type="button" class="btn btn-light">Light</button>
            <button type="button" class="btn btn-dark">Dark</button>
          </div>
        </div>

        <h5>Badges</h5>
        <div class="row mb-4">
          <div class="col">
            <span class="badge bg-primary">Primary</span>
            <span class="badge bg-secondary">Secondary</span>
            <span class="badge bg-success">Success</span>
            <span class="badge bg-danger">Danger</span>
            <span class="badge bg-warning">Warning</span>
            <span class="badge bg-info">Info</span>
            <span class="badge bg-light text-dark">Light</span>
            <span class="badge bg-dark">Dark</span>
          </div>
        </div>

        <h5>Alertes</h5>
        <div class="row mb-4">
          <div class="col">
            <div class="alert alert-primary" role="alert">
              Ceci est une alerte primary
            </div>
            <div class="alert alert-secondary" role="alert">
              Ceci est une alerte secondary
            </div>
            <div class="alert alert-success" role="alert">
              Ceci est une alerte success
            </div>
            <div class="alert alert-danger" role="alert">
              Ceci est une alerte danger
            </div>
            <div class="alert alert-warning" role="alert">
              Ceci est une alerte warning
            </div>
            <div class="alert alert-info" role="alert">
              Ceci est une alerte info
            </div>
          </div>
        </div>

        <h5>Cards</h5>
        <div class="row mb-4">
          <div class="col-md-4">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">Card title</h5>
                <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
                <a href="javascript:void(0)" class="btn btn-primary">Go somewhere</a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <div class="card-header">Featured</div>
              <div class="card-body">
                <h5 class="card-title">Special title treatment</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="javascript:void(0)" class="btn btn-primary">Go somewhere</a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card">
              <img class="card-img-top" src="{{ asset('sneat-1.0.0/assets/img/elements/12.jpg') }}" alt="Card image cap">
              <div class="card-body">
                <h5 class="card-title">Card with image</h5>
                <p class="card-text">This is a wider card with supporting text below as a natural lead-in to additional content.</p>
                <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
              </div>
            </div>
          </div>
        </div>

        <h5>Formulaires</h5>
        <div class="row mb-4">
          <div class="col-md-6">
            <div class="mb-3">
              <label for="exampleFormControlInput1" class="form-label">Email address</label>
              <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
            </div>
            <div class="mb-3">
              <label for="exampleFormControlTextarea1" class="form-label">Example textarea</label>
              <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
            </div>
            <div class="mb-3">
              <label for="exampleFormControlSelect1" class="form-label">Example select</label>
              <select class="form-select" id="exampleFormControlSelect1" aria-label="Default select example">
                <option selected>Open this select menu</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
              <label class="form-check-label" for="defaultCheck1">
                Default checkbox
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault1">
              <label class="form-check-label" for="flexRadioDefault1">
                Default radio
              </label>
            </div>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="flexRadioDefault" id="flexRadioDefault2" checked>
              <label class="form-check-label" for="flexRadioDefault2">
                Default checked radio
              </label>
            </div>
            <div class="form-check form-switch mt-3">
              <input class="form-check-input" type="checkbox" id="flexSwitchCheckDefault">
              <label class="form-check-label" for="flexSwitchCheckDefault">Default switch checkbox input</label>
            </div>
          </div>
        </div>

        <h5>Tableaux</h5>
        <div class="row mb-4">
          <div class="col">
            <div class="table-responsive">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Prénom</th>
                    <th>Nom</th>
                    <th>Poste</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <th scope="row">1</th>
                    <td>Jean</td>
                    <td>Dupont</td>
                    <td>Directeur</td>
                  </tr>
                  <tr>
                    <th scope="row">2</th>
                    <td>Marie</td>
                    <td>Martin</td>
                    <td>Comptable</td>
                  </tr>
                  <tr>
                    <th scope="row">3</th>
                    <td>Pierre</td>
                    <td>Durand</td>
                    <td>Commercial</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
