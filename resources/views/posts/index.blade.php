<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Data Posts</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>



<body style="background: lightgray">

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div>
                    {{-- <h3 class="text-center my-4">DATA PESERTA </h3> --}}
                    <hr>
                </div>
                <div class="card border-0 shadow-sm rounded">
                    <div class="card-body">
                        <div class="d-flex card-header justify-content-between align-items-center">
                            <a href="{{ route('posts.create') }}" class="btn btn-md btn-success mb-3">TAMBAH POST</a>
                            <form class="form-inline mb-3">
                                <input class="form-control mr-sm-2" type="search" placeholder="Search"
                                    aria-label="Search" name="search">
                                <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                            </form>
                        </div>
                        <table class="table table-bordered">
                            <thead>
                                <tr class="text-center">
                                    <th scope="col">GAMBAR</th>
                                    <th scope="col">NAMA</th>
                                    <th scope="col">NISN</th>
                                    <th scope="col">JURUSAN</th>
                                    <th scope="col">Verifikasi</th>
                                    <th scope="col">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($posts as $post)
                                    <tr>
                                        <td class="text-center">
                                            <img src="{{ asset('/storage/posts/' . $post->image) }}" class="rounded"
                                                style="width: 150px">
                                        </td>
                                        <td>{{ $post->name }}</td>
                                        <td>{!! $post->nisn !!}</td>
                                        <td>{!! $post->major !!}</td>
                                        <td class="text-center verification-cell">
                                            <p>data belum tersedia</p>
                                        </td>
                                        <td class="text-center d-flex">
                                            <form onsubmit="return confirm('Apakah Anda Yakin ?');"
                                                action="{{ route('posts.destroy', $post->id) }}" method="POST"
                                                class="ml-5">
                                                <a href="{{ route('posts.show', $post->id) }}"
                                                    class="btn btn-sm btn-dark">SHOW</a>
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger">HAPUS</button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-success btn-verifikasi ml-1"
                                                data-status="proses"
                                                data-post-id="{{ $post->id }}">Verifikasi</button>
                                        </td>
                                    </tr>
                                @empty
                                    <div class="alert alert-danger">
                                        Data Post belum Tersedia.
                                    </div>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        //message with toastr
        @if (session()->has('success'))

            toastr.success('{{ session('success') }}', 'BERHASIL!');
        @elseif (session()->has('error'))

            toastr.error('{{ session('error') }}', 'GAGAL!');
        @endif


        $(document).ready(function() {
            $('.btn-verifikasi').on('click', function() {
                var row = $(this).closest('tr');
                var verificationCell = row.find('.verification-cell');
                var status = $(this).data('status');
                var postId = $(this).data('post-id');

                if (status === 'proses') {
                    verificationCell.html('<p>anda di terima</p>');
                    $(this).removeClass('btn-success').addClass('btn-danger').text('Batalkan').data(
                        'status', 'terima');
                    localStorage.setItem('verification_status_' + postId, 'terima');
                } else {
                    verificationCell.html('<p>data masih di proses</p>');
                    $(this).removeClass('btn-danger').addClass('btn-success').text('Verifikasi').data(
                        'status', 'proses');
                    localStorage.setItem('verification_status_' + postId, 'proses');
                }
            });

            // Restore verification status on page load
            $('.btn-verifikasi').each(function() {
                var postId = $(this).data('post-id');
                var status = localStorage.getItem('verification_status_' + postId);

                if (status === 'terima') {
                    var row = $(this).closest('tr');
                    var verificationCell = row.find('.verification-cell');
                    $(this).removeClass('btn-success').addClass('btn-danger').text('Batalkan').data(
                        'status', 'terima');
                    verificationCell.html('<p>anda di terima</p>');
                }
            });
        });
    </script>

</body>

</html>
