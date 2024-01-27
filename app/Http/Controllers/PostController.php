<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    /**
     * Menampilkan daftar post.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        // Mendapatkan kata kunci pencarian dari input form
        $keyword = $request->input('search');

        // Query untuk mencari data sesuai dengan kata kunci
        $posts = Post::where('name', 'like', "%$keyword%")
            ->orWhere('nisn', 'like', "%$keyword%")
            ->orWhere('major', 'like', "%$keyword%")
            ->latest()
            ->paginate(5);

        return view('posts.index', compact('posts'));
    }

    /**
     * Menampilkan form untuk membuat post baru.
     *
     * @return View
     */
    public function create(): View
    {
        return view('posts.create');
    }

    /**
     * Menyimpan post baru.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'name' => 'required|min:5',
            'nisn' => 'required|numeric', // Sesuaikan dengan kebutuhan
            'major' => 'required|min:10',
        ]);
        
        

        // Upload gambar
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());
        // Log nilai 'nisn'
        \Log::info('Nilai nisn:', ['nisn' => $request->nisn]);

        // Membuat post baru
        Post::create([
            'image' => $image->hashName(),
            'name' => $request->name,
            'nisn' => $request->nisn,
            'major' => $request->major,
        ]);

        // Redirect ke index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    /**
     * Menampilkan detail post.
     *
     * @param string $id
     * @return View
     */
    public function show(string $id): View
    {
        // Mengambil post berdasarkan ID
        $post = Post::findOrFail($id);

        // Menampilkan view dengan data post
        return view('posts.show', compact('post'));
    }

    /**
     * Menghapus post.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy($id): RedirectResponse
    {
        // Mengambil post berdasarkan ID
        $post = Post::findOrFail($id);

        // Menghapus gambar
        Storage::delete('public/posts/' . $post->image);

        // Menghapus post
        $post->delete();

        // Redirect ke index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
}
