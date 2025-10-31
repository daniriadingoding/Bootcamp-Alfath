@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Daftar Menu Makanan') }}</span>
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('foodmenu.create') }}" class="btn btn-primary btn-sm">{{ __('Tambah Menu Baru') }}</a>
                    @endif
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Gambar</th>
                                    <th scope="col">Nama</th>
                                    <th scope="col">Deskripsi</th>
                                    <th scope="col">Harga</th>
                                    @if(Auth::user()->isAdmin())
                                    <th scope="col">Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($foodMenus as $foodMenu)
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>
                                        @if ($foodMenu->image)
                                        <img src="{{ Storage::url($foodMenu->image) }}" alt="{{ $foodMenu->name }}" width="100" class="img-thumbnail">
                                        @else
                                        <span class="text-muted">No Image</span>
                                        @endif
                                    </td>
                                    <td>{{ $foodMenu->name }}</td>
                                    <td>{{ $foodMenu->description }}</td>
                                    <td>Rp {{ number_format($foodMenu->price, 0, ',', '.') }}</td>
                                    @if(Auth::user()->isAdmin())
                                    <td>
                                        <a href="{{ route('foodmenu.edit', $foodMenu->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                        <form action="{{ route('foodmenu.destroy', $foodMenu->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                        </form>
                                    </td>
                                    @endif
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->isAdmin() ? '6' : '5' }}" class="text-center">Tidak ada data menu makanan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection