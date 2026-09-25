@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="mb-4">
        <h1 class="h3">Add Position</h1>
        <p class="text-muted">
            Create a new employee position
        </p>
    </div>

    <div class="card shadow-sm">

        <div class="card-body">

            <form action="{{ route('positions.store') }}" method="POST">

                @csrf

                <div class="mb-3">

                    <label for="division_id" class="form-label">
                        Division
                    </label>

                    <select name="division_id"
                            id="division_id"
                            class="form-select @error('division_id') is-invalid @enderror">

                        <option value="">
                            -- Select Division --
                        </option>

                        @foreach($divisions as $division)

                            <option value="{{ $division->id }}"
                                {{ old('division_id') == $division->id ? 'selected' : '' }}>

                                {{ $division->name }}

                            </option>

                        @endforeach

                    </select>

                    @error('division_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="mb-3">

                    <label for="name" class="form-label">
                        Position Name
                    </label>

                    <input type="text"
                           name="name"
                           id="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}"
                           placeholder="Example: IT Support">

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="mb-3">

                    <label for="description" class="form-label">
                        Description
                    </label>

                    <textarea name="description"
                              id="description"
                              rows="4"
                              class="form-control @error('description') is-invalid @enderror"
                              placeholder="Position description">{{ old('description') }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                <div class="d-flex gap-2">

                    <a href="{{ route('positions.index') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit"
                            class="btn btn-primary">
                        Save Position
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection