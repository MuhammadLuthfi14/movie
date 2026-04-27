<?php

namespace App\Services;

use App\Interfaces\MovieRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class MovieService
{
    protected $movieRepository;

    // Inject Repository melalui Constructor
    public function __construct(MovieRepositoryInterface $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function getAllMovies(int $perPage = 10, string $search = null)
    {
        return $this->movieRepository->getAllPaginated($perPage, $search);
    }

    public function getMovieById($id)
    {
        return $this->movieRepository->findById($id);
    }

    public function getAllCategories()
    {
        return $this->movieRepository->getAllCategories();
    }

    public function createMovie(array $data, $file = null)
    {
        if ($file) {
            // Memanggil private method agar kode tetap clean & reusable
            $data['foto_sampul'] = $this->uploadImage($file);
        }

        return $this->movieRepository->create($data);
    }

    public function updateMovie($id, array $data, $file = null)
    {
        if ($file) {
            $movie = $this->getMovieById($id);
            // Hapus foto lama sebelum mengupload file yang baru
            $this->deleteImage($movie->foto_sampul);
            $data['foto_sampul'] = $this->uploadImage($file);
        }

        return $this->movieRepository->update($id, $data);
    }

    public function deleteMovie($id)
    {
        $movie = $this->getMovieById($id);
        // Otomatis hapus gambar fisik saat data di DB dihapus
        $this->deleteImage($movie->foto_sampul);

        return $this->movieRepository->delete($id);
    }

    /**
     * Clean Code: Single Responsibility untuk Handle Upload File
     */
    private function uploadImage($file): string
    {
        $fileName = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
        // Menyeragamkan penyimpanan menggunakan public_path agar konsisten
        $file->move(public_path('images'), $fileName);
        
        return $fileName;
    }

    /**
     * Clean Code: Single Responsibility untuk Delete File
     */
    private function deleteImage(?string $fileName): void
    {
        if ($fileName && File::exists(public_path('images/' . $fileName))) {
            File::delete(public_path('images/' . $fileName));
        }
    }
}
